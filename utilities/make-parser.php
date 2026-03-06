<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$parser = new \SmartyGenerator\ParserGenerator();
$parser->setQuiet();
$parser->main($argv[1], $argv[2]);

$content = file_get_contents($argv[2]);
$content = preg_replace(['#/\*\s*\d+\s*\*/#', "#'lhs'#", "#'rhs'#"], ['', 0, 1], $content);
file_put_contents($argv[2], $content);
