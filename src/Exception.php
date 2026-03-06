<?php

declare(strict_types=1);

namespace Smarty;

/**
 * Smarty exception class
 */
class Exception extends \Exception
{
    public function __toString(): string
    {
        return ' --> Smarty: ' . $this->message . ' <-- ';
    }
}
