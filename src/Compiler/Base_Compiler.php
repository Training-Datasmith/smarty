<?php

declare (strict_types=1);
namespace Smarty\Compiler;

use Smarty\Smarty;
abstract class Base_Compiler
{
    /**
     * Smarty object
     *
     * @var Smarty
     */
    protected $smarty;
    public function get_smarty(): Smarty
    {
        return $this->smarty;
    }
}