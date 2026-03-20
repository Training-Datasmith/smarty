<?php

declare (strict_types=1);
namespace Smarty\Block_Handler;

use Smarty\Template;
interface Block_Handler_Interface
{
    public function handle($params, $content, Template $template, &$repeat);
    public function is_cacheable(): bool;
}