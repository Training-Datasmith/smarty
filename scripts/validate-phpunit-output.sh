#!/usr/bin/env bash

validate_phpunit_output() {
  local output_file="$1"
  local stripped
  stripped="$(mktemp)"
  sed -E 's/\x1b\[[0-9;]*[[:alpha:]]//g' "${output_file}" >"${stripped}"

  if grep -Eq '^(PHP )?Warning:' "${stripped}"; then
    printf '%s\n' "PHPUnit output contains Warning:"
    grep -E '^(PHP )?Warning:' "${stripped}" || true
    rm -f "${stripped}"
    return 1
  fi
  if grep -Eq '^(PHP )?Deprecated:' "${stripped}"; then
    printf '%s\n' "PHPUnit output contains Deprecated:"
    grep -E '^(PHP )?Deprecated:' "${stripped}" || true
    rm -f "${stripped}"
    return 1
  fi
  if grep -Eq '^(PHP )?Notice:' "${stripped}"; then
    printf '%s\n' "PHPUnit output contains Notice:"
    grep -E '^(PHP )?Notice:' "${stripped}" || true
    rm -f "${stripped}"
    return 1
  fi

  if ! grep -Eq '^OK \([0-9]+ tests?, [0-9]+ assertions\)$' "${stripped}"; then
    printf '%s\n' 'PHPUnit output is missing strict success line: OK (N tests, M assertions)'
    rm -f "${stripped}"
    return 1
  fi
  grep -Eo '^OK \([0-9]+ tests?, [0-9]+ assertions\)$' "${stripped}" | tail -n 1
  rm -f "${stripped}"
  return 0
}
