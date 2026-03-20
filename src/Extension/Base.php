<?php

declare (strict_types=1);
namespace Smarty\Extension;

class Base implements Extension_Interface
{
    public function get_tag_compiler(string $tag): ?\Smarty\Compile\Compiler_Interface
    {
        return null;
    }
    public function get_modifier_compiler(string $modifier): ?\Smarty\Compile\Modifier\Modifier_Compiler_Interface
    {
        return null;
    }
    public function get_function_handler(string $function_name): ?\Smarty\Function_Handler\Function_Handler_Interface
    {
        return null;
    }
    public function get_block_handler(string $block_tag_name): ?\Smarty\Block_Handler\Block_Handler_Interface
    {
        return null;
    }
    public function get_modifier_callback(string $modifier_name)
    {
        return null;
    }
    public function get_pre_filters(): array
    {
        return [];
    }
    public function get_post_filters(): array
    {
        return [];
    }
    public function get_output_filters(): array
    {
        return [];
    }
}