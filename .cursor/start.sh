#!/usr/bin/env bash
set -euo pipefail

if command -v mariadbd-safe >/dev/null 2>&1; then
  sudo mkdir -p /run/mysqld
  sudo chown mysql:mysql /run/mysqld 2>/dev/null || true
  if ! pgrep -x mariadbd >/dev/null 2>&1 && ! pgrep -x mysqld >/dev/null 2>&1; then
    sudo mariadbd-safe --no-watch --skip-syslog &
    sleep 5
  fi
  sudo mariadb -e "CREATE DATABASE IF NOT EXISTS test;" 2>/dev/null \
    || sudo mysql -e "CREATE DATABASE IF NOT EXISTS test;" 2>/dev/null \
    || true
fi
