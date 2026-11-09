<?php

declare(strict_types=1);

use Smarty\Compiler\Template;
use Smarty\Smarty;

/**
 * Real compiler instance for tests that need a Template type without PHPUnit mocking it.
 */
class Template_Compiler_Test_Stub extends Template
{
    public function __construct(Smarty $smarty)
    {
        parent::__construct($smarty);
    }
}
