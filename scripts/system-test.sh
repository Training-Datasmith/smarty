#!/usr/bin/env bash
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
COMPOSE_FILE="${REPO_ROOT}/docker-compose.system-test.yml"
COMPOSE_PROJECT="smarty-system-test"
USE_SUDO_DOCKER="${USE_SUDO_DOCKER:-0}"
COMPOSER_SHA256="28dbb6bd8bef31479c7985b774c130a8bda37dbe63c35b56f6cb6bc377427573"
COMPOSER_URL="https://getcomposer.org/download/2.8.10/composer.phar"
COMPOSER_PHAR="${REPO_ROOT}/.system-test/composer.phar"

docker_cmd() {
    if [[ "${USE_SUDO_DOCKER}" == "1" ]]; then
        sudo docker "$@"
    else
        docker "$@"
    fi
}

mkdir -p "${REPO_ROOT}/.system-test"
if [[ ! -f "${COMPOSER_PHAR}" ]]; then
    curl -fsSL -o "${COMPOSER_PHAR}" "${COMPOSER_URL}"
    echo "${COMPOSER_SHA256}  ${COMPOSER_PHAR}" | sha256sum -c -
    chmod +x "${COMPOSER_PHAR}"
fi

docker_cmd compose -f "${COMPOSE_FILE}" -p "${COMPOSE_PROJECT}" build app
docker_cmd compose -f "${COMPOSE_FILE}" -p "${COMPOSE_PROJECT}" up -d mariadb

docker_cmd compose -f "${COMPOSE_FILE}" -p "${COMPOSE_PROJECT}" run --rm --no-TTY app \
    bash /app/scripts/system-test-inner.sh

docker_cmd compose -f "${COMPOSE_FILE}" -p "${COMPOSE_PROJECT}" stop mariadb
