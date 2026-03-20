<?php

declare (strict_types=1);
namespace Smarty\Runtime;

use Smarty\Exception;
use Smarty\Template;
use Smarty\Template\Source;
/**
 * Inheritance Runtime Methods processBlock, endChild, init
 *
 * @author     Uwe Tews
 **/
class Inheritance_Runtime
{
    /**
     * State machine
     * - 0 idle next extends will create a new inheritance tree
     * - 1 processing child template
     * - 2 wait for next inheritance template
     * - 3 assume parent template, if child will loaded goto state 1
     *     a call to a sub template resets the state to 0
     *
     * @var int
     */
    private $state = 0;
    /**
     * Array of root child {block} objects
     *
     * @var \Smarty\Runtime\Block[]
     */
    private $child_root = [];
    /**
     * inheritance template nesting level
     *
     * @var int
     */
    private $inheritance_level = 0;
    /**
     * inheritance template index
     *
     * @var int
     */
    private $tpl_index = -1;
    /**
     * Array of template source objects
     *
     * @var Source[]
     */
    private $sources = [];
    /**
     * Stack of source objects while executing block code
     *
     * @var Source[]
     */
    private $source_stack = [];
    /**
     * Initialize inheritance
     *
     * @param \Smarty\Template $tpl template object of caller
     * @param bool $initChild if true init for child template
     * @param array $blockNames outer level block name
     */
    public function init(Template $tpl, $init_child, $block_names = []): void
    {
        // if called while executing parent template it must be a sub-template with new inheritance root
        if ($init_child && $this->state === 3 && strpos($tpl->template_resource, 'extendsall') === false) {
            $tpl->set_inheritance(clone $tpl->get_smarty()->get_runtime('Inheritance'));
            $tpl->get_inheritance()->init($tpl, $init_child, $block_names);
            return;
        }
        ++$this->tpl_index;
        $this->sources[$this->tpl_index] = $tpl->get_source();
        // start of child sub template(s)
        if ($init_child) {
            $this->state = 1;
            if (!$this->inheritance_level) {
                //grab any output of child templates
                ob_start();
            }
            ++$this->inheritance_level;
        }
        // if state was waiting for parent change state to parent
        if ($this->state === 2) {
            $this->state = 3;
        }
    }
    /**
     * End of child template(s)
     * - if outer level is reached flush output buffer and switch to wait for parent template state
     *
     * @param null|string $template optional name of inheritance parent template
     *
     * @throws \Exception
     * @throws \Smarty\Exception
     */
    public function end_child(Template $tpl, $template = null, ?string $current_dir = null): void
    {
        --$this->inheritance_level;
        if (!$this->inheritance_level) {
            ob_end_clean();
            $this->state = 2;
        }
        if (isset($template)) {
            $tpl->render_sub_template($template, $tpl->cache_id, $tpl->compile_id, $tpl->caching ? \Smarty\Template::CACHING_NOCACHE_CODE : 0, $tpl->cache_lifetime, [], null, $current_dir);
        }
    }
    /**
     * \Smarty\Runtime\Block constructor.
     * - if outer level {block} of child template ($state === 1) save it as child root block
     * - otherwise process inheritance and render
     *
     * @param                           $className
     * @param string $name
     * @param int|null $tplIndex index of outer level {block} if nested
     * @throws \Smarty\Exception
     */
    public function instance_block(Template $tpl, $class_name, $name, $tpl_index = null): void
    {
        $block = new $class_name($name, $tpl_index ?? $this->tpl_index);
        if (isset($this->child_root[$name])) {
            $block->child = $this->child_root[$name];
        }
        if ($this->state === 1) {
            $this->child_root[$name] = $block;
            return;
        }
        // make sure we got child block of child template of current block
        while ($block->child && $block->child->child && $block->tpl_index <= $block->child->tpl_index) {
            $block->child = $block->child->child;
        }
        $this->process_block($tpl, $block);
    }
    /**
     * Goto child block or render this
     *
     *
     * @throws Exception
     */
    private function process_block(Template $tpl, \Smarty\Runtime\Block $block, ?\Smarty\Runtime\Block $parent = null): void
    {
        if ($block->hide && !isset($block->child)) {
            return;
        }
        if (isset($block->child) && $block->child->hide && !isset($block->child->child)) {
            $block->child = null;
        }
        $block->parent = $parent;
        if ($block->append && !$block->prepend && isset($parent)) {
            $this->call_parent($tpl, $block);
        }
        if ($block->calls_child || !isset($block->child) || $block->child->hide && !isset($block->child->child)) {
            $this->call_block($block, $tpl);
        } else {
            $this->process_block($tpl, $block->child, $block);
        }
        if ($block->prepend && isset($parent)) {
            $this->call_parent($tpl, $block);
            if ($block->append) {
                if ($block->calls_child || !isset($block->child) || $block->child->hide && !isset($block->child->child)) {
                    $this->call_block($block, $tpl);
                } else {
                    $this->process_block($tpl, $block->child, $block);
                }
            }
        }
        $block->parent = null;
    }
    /**
     * Render child on \$smarty.block.child
     *
     *
     * @return null|string block content
     * @throws Exception
     */
    public function call_child(Template $tpl, \Smarty\Runtime\Block $block): void
    {
        if (isset($block->child)) {
            $this->process_block($tpl, $block->child, $block);
        }
    }
    /**
     * Render parent block on \$smarty.block.parent or {block append/prepend}
     *
     * @param string $tag
     * @return null|string  block content
     * @throws Exception
     */
    public function call_parent(Template $tpl, \Smarty\Runtime\Block $block): void
    {
        if (isset($block->parent)) {
            $this->call_block($block->parent, $tpl);
        } else {
            throw new Exception("inheritance: illegal '{\$smarty.block.parent}' used in child template '" . "{$tpl->get_inheritance()->sources[$block->tpl_index]->get_resource_name()}' block '{$block->name}'");
        }
    }
    /**
     * render block
     */
    public function call_block(\Smarty\Runtime\Block $block, Template $tpl): void
    {
        $this->source_stack[] = $tpl->get_source();
        $tpl->set_source($this->sources[$block->tpl_index]);
        $block->call_block($tpl);
        $tpl->set_source(array_pop($this->source_stack));
    }
}