<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
class Bc_Plugin_Wrapper extends Base
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = ['_any'];
    private $callback;
    public function __construct($callback, bool $cacheable = true)
    {
        $this->callback = $callback;
        $this->cacheable = $cacheable;
    }
    /**
     * @inheritDoc
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        return call_user_func($this->callback, $this->get_attributes($compiler, $args), $compiler->get_smarty());
    }
}