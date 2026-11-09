<?php

declare (strict_types=1);
namespace Smarty\Filter;

class Filter_Plugin_Wrapper implements Filter_Interface
{
    private $callback;
    public function __construct($callback)
    {
        $this->callback = $callback;
    }
    public function filter($code, \Smarty\Template $template)
    {
        return call_user_func($this->callback, $code, $template);
    }
}