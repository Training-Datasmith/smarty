# Architecture: smarty

## Purpose

Smarty is a PHP template engine that separates application logic from presentation. Templates use a `{tag}` syntax for variables, control structures, and function calls; Smarty compiles them to plain PHP files that are cached and reused on subsequent requests.

## Directory Structure

```
src/
  Smarty.php                - Main entry point: configure and render templates
  Data.php                  - Template data/variable scope container
  Security.php              - Security policy: restrict tags, modifiers, PHP access
  Debug.php                 - Debug console output
  Error_Handler.php         - Custom PHP error handler for template errors
  Exception.php             - Base Smarty exception
  Compiler_Exception.php    - Thrown on template compilation errors
  Lexer/                    - Tokeniser: breaks template source into tokens
  Parser/                   - Parser: turns token stream into an AST
  Compile/                  - Code generators: emit PHP from AST nodes
  Compiler/                 - Orchestrates lex → parse → compile pipeline
  Runtime/                  - Runtime helpers called by compiled template PHP
  Template/                 - Template object: manage source, compiled, cached files
  BlockHandler/             - Block tag implementations ({block}{/block})
  FunctionHandler/          - Function tag implementations ({html_options}, etc.)
  Extension/                - Extension API for custom tags, modifiers, resources
  Filter/                   - Pre/post/output filters applied to template source or output
  Cacheresource/            - Cache backends (file, custom)
  Resource/                 - Template source backends (file, string, DB)
  ParseTree/                - Parse tree node types produced by the parser
```

## Key Design Decisions

- **Compile-then-cache**: Templates are compiled to PHP once and cached as `.php` files. On subsequent requests, Smarty checks the modification time of the source and only recompiles when the source changes, making repeated rendering nearly as fast as plain PHP.
- **Security sandbox**: `Security.php` implements a whitelist/blacklist policy for which PHP functions, modifiers, and tags are accessible from templates, preventing template injection attacks in multi-user or CMS environments.
- **Pluggable resource system**: Template source can come from any backend (file, database, string) by implementing a `Resource` class. Similarly, caching can use any storage by implementing a `Cacheresource` class.
- **Extension API**: Custom tags (functions and block tags), modifiers, and filters are registered as extensions, keeping user code out of the core.

## Extension Points

- Register custom tags via `$smarty->registerPlugin('function', 'my_tag', $callback)`.
- Register custom modifiers via `$smarty->registerPlugin('modifier', 'my_mod', $callback)`.
- Add custom template sources by implementing a `Smarty_Resource_Custom` subclass.
- Add custom cache backends by implementing a `Smarty_CacheResource` subclass.
- Add pre/post/output filters via `$smarty->registerFilter('output', $callback)`.

## Dependency Flow

```
$smarty->display('template.tpl')
  └─> Resource — locate and load template source
  └─> Compiler (Lexer → Parser → Compile) — compile .tpl to .php (first request only)
  └─> Cacheresource — check/write cache
  └─> include compiled PHP — execute template, call Runtime helpers
  └─> Filter::output — apply output filters
  └─> send rendered HTML to output buffer
```
