#!/usr/bin/env bash
set -euo pipefail

export COMPOSER_ALLOW_SUPERUSER=1

fail_deprecation_output() {
    local label="$1"
    local file="$2"
    local stripped
    stripped="$(mktemp)"
    sed -E 's/\x1b\[[0-9;]*[[:alpha:]]//g' "${file}" >"${stripped}"
    if grep -E '(^|[[:space:]])(PHP[[:space:]]+)?(Warning|Deprecated|Notice):' "${stripped}" >/dev/null; then
        echo "${label}: output contains Warning, Deprecated, or Notice lines" >&2
        grep -E '(^|[[:space:]])(PHP[[:space:]]+)?(Warning|Deprecated|Notice):' "${stripped}" >&2 || true
        rm -f "${stripped}"
        exit 1
    fi
    rm -f "${stripped}"
}

git config --global --add safe.directory /app >/dev/null 2>&1 || true

echo "28dbb6bd8bef31479c7985b774c130a8bda37dbe63c35b56f6cb6bc377427573  /app/.system-test/composer.phar" | sha256sum -c -

composer_out="$(mktemp)"
set +e
php /app/.system-test/composer.phar update --no-interaction --prefer-dist --no-progress >"${composer_out}" 2>&1
composer_status=$?
set -e
cat "${composer_out}"
fail_deprecation_output "composer update" "${composer_out}"
if [[ ${composer_status} -ne 0 ]]; then
    rm -f "${composer_out}"
    exit "${composer_status}"
fi
rm -f "${composer_out}"

php scripts/check-cluster-pins.php --wrong-installed-php-demo
php scripts/check-cluster-pins.php --wrong-installed-json-demo

audit_out="$(mktemp)"
set +e
php /app/.system-test/composer.phar audit >"${audit_out}" 2>&1
audit_status=$?
set -e
cat "${audit_out}"
fail_deprecation_output "composer audit" "${audit_out}"
if [[ ${audit_status} -ne 0 ]]; then
    exit "${audit_status}"
fi
if ! grep -Fxq 'No security vulnerability advisories found.' "${audit_out}"; then
    echo 'composer audit: missing exact success line' >&2
    exit 1
fi
rm -f "${audit_out}"

php scripts/check-cluster-pins.php

php scripts/check-rename-residue.php --demo-inject
php scripts/check-rename-residue.php --demo-class-mismatch
php scripts/check-rename-residue.php --demo-grammar-old
php scripts/check-rename-residue.php --demo-camel

php scripts/check-rename-residue.php

phpunit_out="$(mktemp)"
set +e
vendor/bin/phpunit --colors=never >"${phpunit_out}" 2>&1
phpunit_status=$?
set -e
cat "${phpunit_out}"
fail_deprecation_output "phpunit" "${phpunit_out}"
if [[ ${phpunit_status} -ne 0 ]]; then
    rm -f "${phpunit_out}"
    exit "${phpunit_status}"
fi

# shellcheck source=scripts/validate-phpunit-output.sh
source /app/scripts/validate-phpunit-output.sh
if ! ok_line="$(validate_phpunit_output "${phpunit_out}")"; then
    rm -f "${phpunit_out}"
    exit 1
fi

baseline="/app/scripts/suite-baseline.txt"
if [[ ! -r "${baseline}" ]]; then
    echo "suite baseline missing at ${baseline}" >&2
    rm -f "${phpunit_out}"
    exit 1
fi
expected="$(tr -d '\r\n' < "${baseline}")"
if [[ "${ok_line}" != "${expected}" ]]; then
    echo "PHPUnit summary must match baseline." >&2
    echo "  expected: ${expected}" >&2
    echo "  actual:   ${ok_line}" >&2
    rm -f "${phpunit_out}"
    exit 1
fi
rm -f "${phpunit_out}"

echo 'system-test: all steps passed.'
