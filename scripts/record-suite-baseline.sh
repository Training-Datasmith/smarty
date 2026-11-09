#!/usr/bin/env bash
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
IMAGE_TAG="smarty-system-test:baseline"
USE_SUDO_DOCKER="${USE_SUDO_DOCKER:-0}"
COMPOSER_SHA256="28dbb6bd8bef31479c7985b774c130a8bda37dbe63c35b56f6cb6bc377427573"
COMPOSER_URL="https://getcomposer.org/download/2.8.10/composer.phar"

docker_cmd() {
    if [[ "${USE_SUDO_DOCKER}" == "1" ]]; then
        sudo docker "$@"
    else
        docker "$@"
    fi
}

docker_cmd build -f "${REPO_ROOT}/docker/system-test/Dockerfile" -t "${IMAGE_TAG}" "${REPO_ROOT}"

BASELINE_FILE="${REPO_ROOT}/scripts/suite-baseline.txt"
PHPUNIT_OUT="$(mktemp)"

set +e
docker_cmd compose -f "${REPO_ROOT}/docker-compose.system-test.yml" -p smarty-baseline run --rm --no-TTY app \
    bash -lc "$(cat <<'INNER'
set -euo pipefail
export COMPOSER_ALLOW_SUPERUSER=1

echo "28dbb6bd8bef31479c7985b774c130a8bda37dbe63c35b56f6cb6bc377427573  /app/.system-test/composer.phar" | sha256sum -c -
php /app/.system-test/composer.phar update --no-interaction --prefer-dist --no-progress

vendor/bin/phpunit --colors=never 2>&1 | tee /tmp/phpunit.out
INNER
)" >"${PHPUNIT_OUT}" 2>&1
status=$?
set -e

cat "${PHPUNIT_OUT}"

if [[ ${status} -ne 0 ]]; then
    rm -f "${PHPUNIT_OUT}"
    exit "${status}"
fi

# shellcheck source=scripts/validate-phpunit-output.sh
source "${REPO_ROOT}/scripts/validate-phpunit-output.sh"
if ! ok_line="$(validate_phpunit_output "${PHPUNIT_OUT}")"; then
    rm -f "${PHPUNIT_OUT}"
    exit 1
fi

printf '%s\n' "${ok_line}" >"${BASELINE_FILE}"
echo "Recorded baseline: $(cat "${BASELINE_FILE}")"
rm -f "${PHPUNIT_OUT}"
