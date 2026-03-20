<?php

declare (strict_types=1);
namespace Smarty\Extension;

use Smarty\Block_Handler\Block_Handler_Interface;
use Smarty\Compile\Compiler_Interface;
use Smarty\Compile\Modifier\Modifier_Compiler_Interface;
use Smarty\Function_Handler\Function_Handler_Interface;
interface Extension_Interface
{
    /**
     * Either return \Smarty\Compile\CompilerInterface that will compile the given $tag or
     * return null to indicate that you do not know how to handle this $tag. (Another Extension might.)
     */
    public function get_tag_compiler(string $tag): ?Compiler_Interface;
    /**
     * Either return \Smarty\Compile\Modifier\ModifierCompilerInterface that will compile the given $modifier or
     * return null to indicate that you do not know how to handle this $modifier. (Another Extension might.)
     */
    public function get_modifier_compiler(string $modifier): ?Modifier_Compiler_Interface;
    /**
     * Either return \Smarty\FunctionHandler\FunctionHandlerInterface that will handle the given $functionName or
     * return null to indicate that you do not know how to handle this $functionName. (Another Extension might.)
     */
    public function get_function_handler(string $function_name): ?Function_Handler_Interface;
    /**
     * Either return \Smarty\BlockHandler\BlockHandlerInterface that will handle the given $blockTagName or return null
     * to indicate that you do not know how to handle this $blockTagName. (Another Extension might.)
     */
    public function get_block_handler(string $block_tag_name): ?Block_Handler_Interface;
    /**
     * Either return a callable that takes at least 1 parameter (a string) and returns a modified string or return null
     * to indicate that you do not know how to handle this $modifierName. (Another Extension might.)
     *
     * The callable can accept additional optional parameters.
     *
     * @return callable|null
     */
    public function get_modifier_callback(string $modifier_name);
    /**
     * Return a list of prefilters that will all be applied, in sequence.
     * Template prefilters can be used to preprocess templates before they are compiled.
     *
     * @return \Smarty\Filter\FilterInterface[]
     */
    public function get_pre_filters(): array;
    /**
     * Return a list of postfilters that will all be applied, in sequence.
     * Template postfilters can be used to process compiled template code (so, after the compilation).
     *
     * @return \Smarty\Filter\FilterInterface[]
     */
    public function get_post_filters(): array;
    /**
     * Return a list of outputfilters that will all be applied, in sequence.
     * Template outputfilters can be used to change template output just before it is rendered.
     *
     * @return \Smarty\Filter\FilterInterface[]
     */
    public function get_output_filters(): array;
}