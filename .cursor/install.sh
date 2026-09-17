#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if ! php -m | grep -qi pdo_mysql; then
  echo "pdo_mysql extension is required for Smarty PDO cache tests" >&2
  exit 1
fi

composer install --no-interaction --prefer-dist
