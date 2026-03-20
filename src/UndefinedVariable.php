<?php

declare (strict_types=1);
namespace Smarty;

/**
 * class for undefined variable object
 * This class defines an object for undefined variable handling
 */
class Undefined_Variable extends Variable
{
    /**
     * Always returns an empty string.
     */
    public function __toString(): string
    {
        return '';
    }
}