<?php

declare (strict_types=1);
namespace Smarty\Function_Handler;

use Smarty\Template;
interface Function_Handler_Interface
{
    public function handle($params, Template $template);
    public function is_cacheable(): bool;
}