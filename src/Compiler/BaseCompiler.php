<?php

declare(strict_types=1);

namespace Smarty\Compiler;

use Smarty\Smarty;

abstract class BaseCompiler
{
    /**
     * Smarty object
     *
     * @var Smarty
     */
    protected $smarty;

    public function getSmarty(): Smarty
    {
        return $this->smarty;
    }

}
