#!/usr/bin/env python3
"""Rename test classes to match _Test.php basenames and snake_case PHPUnit_Smarty helpers."""

from __future__ import annotations

import json
import os
import re
import subprocess
import sys

REPO = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TESTS = os.path.join(REPO, "tests")

CLASS_RE = re.compile(
    r"^\s*((?:abstract\s+|final\s+)?class\s+)([A-Za-z_][A-Za-z0-9_]*)",
    re.M,
)

METHOD_MAP = {
    "setUpSmarty": "set_up_smarty",
    "getConnection": "get_connection",
    "initMysqlResource": "init_mysql_resource",
    "initMysqlCache": "init_mysql_cache",
    "cleanDirs": "clean_dirs",
    "makeTemplateFile": "make_template_file",
    "cleanCompileDir": "clean_compile_dir",
    "cleanCacheDir": "clean_cache_dir",
    "cleanDir": "clean_dir",
    "getPDO": "get_pdo",
    "normalizeString": "normalize_string",
    "buildSourcePath": "build_source_path",
    "buildUid": "build_uid",
    "normalizePath": "normalize_path",
    "buildCompiledPath": "build_compiled_path",
    "buildCachedPath": "build_cached_path",
    "prefilterTest": "prefilter_test",
    "getSmartyPluginsDir": "get_smarty_plugins_dir",
    "doClearCacheAssertion": "do_clear_cache_assertion",
    "compiledPrefilter": "compiled_prefilter",
    "dataDir": "data_dir",
    "expectedCachedFilepath": "expected_cached_filepath",
}

PROP_MAP = {
    "$testNumber": "$test_number",
    "$pluginsdir": "$plugins_dir",
    "$touchResource": "$touch_resource",
    "$directorySeparator": "$directory_separator",
    "$lockTime": "$lock_time",
    "$dbConn": "$db_conn",
    "$fetchStatements": "$fetch_statements",
    "$insertStatement": "$insert_statement",
    "$deleteStatement": "$delete_statement",
    "$truncateStatement": "$truncate_statement",
    "$fetchColumns": "$fetch_columns",
    "$fetchTimestampColumns": "$fetch_timestamp_columns",
    "$isNull": "$is_null",
    "$isSet": "$is_set",
}


def collect_class_renames() -> list[tuple[str, str]]:
    renames: list[tuple[str, str]] = []
    for dp, _, fns in os.walk(TESTS):
        for fn in fns:
            if not fn.endswith(".php"):
                continue
            if "templates_c" in dp.replace("\\", "/"):
                continue
            path = os.path.join(dp, fn)
            text = open(path, encoding="utf-8", errors="replace").read()
            m = CLASS_RE.search(text)
            if not m:
                continue
            stem = fn[:-4]
            if not (stem.endswith("_Test") or stem.endswith("Test")):
                continue
            old = m.group(2)
            if old != stem:
                renames.append((old, stem))
    return renames


def replace_word(text: str, old: str, new: str) -> str:
    return re.sub(r"\b" + re.escape(old) + r"\b", new, text)


def walk_php_files() -> list[str]:
    paths: list[str] = []
    for dp, dns, fns in os.walk(TESTS):
        if "templates_c" in dp.replace("\\", "/"):
            dns.clear()
            continue
        for fn in fns:
            if fn.endswith(".php"):
                paths.append(os.path.join(dp, fn))
    return paths


def main() -> int:
    renames = collect_class_renames()
    renames.sort(key=lambda x: len(x[0]), reverse=True)

    for old, new in renames:
        if old == new:
            continue
        for path in walk_php_files():
            text = open(path, encoding="utf-8", errors="replace").read()
            new_text = text
            if path.endswith("/" + new + ".php") or path.endswith(new + ".php"):
                new_text = CLASS_RE.sub(r"\1" + new, new_text, count=1)
            new_text = replace_word(new_text, old, new)
            if new_text != text:
                open(path, "w", encoding="utf-8").write(new_text)

    for path in walk_php_files() + [os.path.join(REPO, "src", "Extension", "Extension_Interface.php")]:
        if not os.path.isfile(path):
            continue
        text = open(path, encoding="utf-8", errors="replace").read()
        new_text = text
        for old, new in METHOD_MAP.items():
            new_text = replace_word(new_text, old, new)
        for old, new in PROP_MAP.items():
            new_text = new_text.replace(old, new)
        if new_text != text:
            open(path, "w", encoding="utf-8").write(new_text)

    print(f"Applied {len(renames)} class renames", file=sys.stderr)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
