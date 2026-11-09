# Smarty strict system test runner

The authoritative suite gate is `scripts/system-test.sh`. It runs inside a digest-pinned PHP **8.1** CLI image with MariaDB for PDO cache tests.

## Why PHP 8.1 instead of 7.2?

`composer.json` keeps the published library constraint `php: ^7.2 || ^8.0` unchanged (matching upstream Smarty). The current tree on `remote-test-2026-09-16` is not parseable on PHP 7.2: it uses typed properties, PHP 7.4 typed property promotion on `Security`, and PHP 8.0 union types (for example `array|int` on the template compiler). PHPUnit 8.5.52+ (required for a clean `composer audit` on the PHP 7.x floor) still runs on PHP 8.1, which also executes backed-enum coverage without extra deprecation noise from PHP 8.2 dynamic-property rules.

The runner therefore exercises the real codebase on **8.1.31** via image digest `php:8.1-cli@sha256:76e563191d1ade120313a8736df24154d21da5155c0756f147c0b01bd19d9087`, while the declared Composer `php` constraint remains the upstream floor range.

## Usage

```bash
USE_SUDO_DOCKER=0 ./scripts/system-test.sh
```

`USE_SUDO_DOCKER` defaults to `0`. Set it to `1` only when Docker requires sudo on the host.

Gates use ANSI-stripped, anchored full success lines for Composer audit, cluster pin checks, rename residue checks, and PHPUnit summary output compared to `scripts/suite-baseline.txt`.
