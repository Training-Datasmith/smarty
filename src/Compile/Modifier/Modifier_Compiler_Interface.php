<?php

declare (strict_types=1);
namespace Smarty\Compile\Modifier;

interface Modifier_Compiler_Interface
{
    /**
     * Compiles code for the modifier
     *
     * @param array $params array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     */
    public function compile($params, \Smarty\Compiler\Template $compiler);
}