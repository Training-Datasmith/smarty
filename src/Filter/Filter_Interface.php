<?php

declare (strict_types=1);
namespace Smarty\Filter;

interface Filter_Interface
{
    public function filter($code, \Smarty\Template $template);
}