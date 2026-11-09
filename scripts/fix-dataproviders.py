#!/usr/bin/env python3
"""Align @dataProvider docblocks and fix doubled underscores in test method names."""

from __future__ import annotations

import os
import re

TESTS = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "tests")


def full_snake(name: str) -> str:
    if name.startswith("test") and len(name) > 4:
        return name
    s1 = re.sub(r"(.)([A-Z][a-z]+)", r"\1_\2", name)
    s2 = re.sub(r"([a-z0-9])([A-Z])", r"\1_\2", s1)
    return re.sub(r"_+", "_", s2.lower())


def fix_file(text: str) -> str:
    text = re.sub(
        r"@dataProvider\s+(\S+)",
        lambda m: "@dataProvider " + full_snake(m.group(1)),
        text,
    )

    def fix_name(name: str) -> str:
        return re.sub(r"_+", "_", name)

    keep = frozenset({"assertEquals", "assertSame", "assertTrue", "assertFalse", "assertNull", "assertCount", "assertEmpty", "assertFileExists", "assertStringContainsString", "assertMatchesRegularExpression", "toJson", "toArray", "getName", "getNext", "setSegment", "getCompiledFilepath"})

    def fix_func(m: re.Match[str]) -> str:
        raw = m.group(1)
        if raw.startswith("__"):
            return f"function {raw}("
        name = raw if raw in keep else fix_name(raw)
        return f"function {name}("

    text = re.sub(r"function\s+([A-Za-z_][A-Za-z0-9_]*)\s*\(", fix_func, text)
    return text


def main() -> None:
    for dp, _, fns in os.walk(TESTS):
        if "templates_c" in dp.replace("\\", "/"):
            continue
        if "/cache/" in dp.replace("\\", "/"):
            continue
        for fn in fns:
            if not fn.endswith(".php") or fn.endswith(".tpl.php"):
                continue
            path = os.path.join(dp, fn)
            rel = path.replace("\\", "/")
            if "/cache/" in rel or not os.access(path, os.W_OK):
                continue
            old = open(path, encoding="utf-8", errors="replace").read()
            new = fix_file(old)
            if new != old:
                open(path, "w", encoding="utf-8").write(new)


if __name__ == "__main__":
    main()
