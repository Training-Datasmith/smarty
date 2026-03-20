<?php

declare (strict_types=1);
namespace Smarty\Function_Handler;

/**
 * Function handler interface with support for specifying supported properties
 */
interface Attribute_Function_Handler_Interface extends Function_Handler_Interface
{
    /**
     * Returns an array with the supported attributes, flags, and shorttags
     * @return array<string, array>
     */
    public function get_supported_attributes(): array;
}