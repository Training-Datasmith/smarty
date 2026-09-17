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
  sudo mariadb -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD(''); FLUSH PRIVILEGES;" 2>/dev/null \
    || sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD(''); FLUSH PRIVILEGES;" 2>/dev/null \
    || true
  if command -v mariadb-tzinfo-to-sql >/dev/null 2>&1; then
    if ! sudo mariadb -N -e "SELECT 1 FROM mysql.time_zone_name WHERE Name = 'Europe/Berlin' LIMIT 1" 2>/dev/null | grep -q 1; then
      sudo mariadb-tzinfo-to-sql /usr/share/zoneinfo 2>/dev/null | sudo mariadb mysql 2>/dev/null || true
    fi
  fi
fi
