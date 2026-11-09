<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

class Bc_Plugin_Wrapper extends Base
{
    private $callback;
    public function __construct($callback)
    {
        $this->callback = $callback;
    }
    /**
     * @inheritDoc
     */
    public function compile($params, \Smarty\Compiler\Template $compiler)
    {
        return call_user_func($this->callback, $params, $compiler);
    }
}