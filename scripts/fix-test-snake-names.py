#!/usr/bin/env python3
"""Fix hybrid snake names and camelCase call sites in tests after bulk codemod."""

from __future__ import annotations

import os
import re

REPO = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TESTS = os.path.join(REPO, "tests")

PHPUNIT_NAMES = frozenset(
    {
        "setUp",
        "tearDown",
        "setUpBeforeClass",
        "tearDownAfterClass",
    }
)


def full_snake(name: str) -> str:
    if name in PHPUNIT_NAMES:
        return name
    if name.startswith("test") and len(name) > 4:
        return name
    if name.startswith("__"):
        return name
    s1 = re.sub(r"(.)([A-Z][a-z]+)", r"\1_\2", name)
    s2 = re.sub(r"([a-z0-9])([A-Z])", r"\1_\2", s1)
    return s2.lower()


def collect_php_files() -> list[str]:
    paths: list[str] = []
    for dp, _, fns in os.walk(TESTS):
        if "templates_c" in dp.replace("\\", "/"):
            continue
        for fn in fns:
            if fn.endswith(".php"):
                paths.append(os.path.join(dp, fn))
    return paths


def build_rename_map(paths: list[str]) -> dict[str, str]:
    rename: dict[str, str] = {}
    func_re = re.compile(r"\bfunction\s+&?\s*([A-Za-z_][A-Za-z0-9_]*)\s*\(")
    for path in paths:
        text = open(path, encoding="utf-8", errors="replace").read()
        for m in func_re.finditer(text):
            old = m.group(1)
            new = full_snake(old)
            if new != old and old not in rename:
                rename[old] = new
    # Common camelCase call-only helpers from PHPUnit_Smarty era
    extra = {
        "runClearAll": "run_clear_all",
        "runClearTemplate": "run_clear_template",
        "runClearOtherTemplate": "run_clear_other_template",
        "runClearCompileid": "run_clear_compileid",
        "runClearOtherCompileid": "run_clear_other_compileid",
        "runClearExpired": "run_clear_expired",
        "runClearTemplateExpired": "run_clear_template_expired",
        "runClearTemplateCacheidExpired": "run_clear_template_cacheid_expired",
        "runClearCacheidExpired": "run_clear_cacheid_expired",
        "runClearTemplateCacheid": "run_clear_template_cacheid",
        "runClearAmbiguousTemplate": "run_clear_ambiguous_template",
        "makeFiles": "make_files",
        "clearFiles": "clear_files",
        "expectFiles": "expect_files",
        "touchFiles": "touch_files",
        "getFiles": "get_files",
        "removeFiles": "remove_files",
    }
    for old, new in extra.items():
        if old not in rename:
            rename[old] = new
    return rename


def apply_renames(text: str, rename: dict[str, str]) -> str:
    by_len = sorted(rename.items(), key=lambda kv: len(kv[0]), reverse=True)
    for old, new in by_len:
        if old == new:
            continue
        text = re.sub(rf"\b{re.escape(old)}\b", new, text)
    return text


def main() -> int:
    paths = collect_php_files()
    rename = build_rename_map(paths)
    for path in paths:
        old = open(path, encoding="utf-8", errors="replace").read()
        new = apply_renames(old, rename)
        if new != old:
            open(path, "w", encoding="utf-8").write(new)
    print(f"Applied {len(rename)} identifier renames across tests/")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
