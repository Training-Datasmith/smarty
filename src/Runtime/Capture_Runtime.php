<?php

declare (strict_types=1);
namespace Smarty\Runtime;

use Smarty\Template;
/**
 * Runtime Extension Capture
 *
 * @author     Uwe Tews
 */
class Capture_Runtime
{
    /**
     * Stack of capture parameter
     *
     * @var array
     */
    private $capture_stack = [];
    /**
     * Current open capture sections
     *
     * @var int
     */
    private $capture_count = 0;
    /**
     * Count stack
     *
     * @var int[]
     */
    private $count_stack = [];
    /**
     * Named buffer
     *
     * @var string[]
     */
    private $named_buffer = [];
    /**
     * Open capture section
     *
     * @param string $buffer capture name
     * @param string $assign variable name
     * @param string $append variable name
     */
    public function open(Template $_template, $buffer, $assign, $append): void
    {
        $this->register_callbacks($_template);
        $this->capture_stack[] = [$buffer, $assign, $append];
        $this->capture_count++;
        ob_start();
    }
    /**
     * Register callbacks in template class
     */
    private function register_callbacks(Template $_template): void
    {
        foreach ($_template->start_render_callbacks as $callback) {
            if (is_array($callback) && get_class($callback[0]) == self::class) {
                // already registered
                return;
            }
        }
        $_template->start_render_callbacks[] = [$this, 'startRender'];
        $_template->end_render_callbacks[] = [$this, 'endRender'];
        $this->start_render($_template);
    }
    /**
     * Start render callback
     */
    public function start_render(Template $_template): void
    {
        $this->count_stack[] = $this->capture_count;
        $this->capture_count = 0;
    }
    /**
     * Close capture section
     *
     *
     * @throws \Smarty\Exception
     */
    public function close(Template $_template): void
    {
        if ($this->capture_count) {
            [$buffer, $assign, $append] = array_pop($this->capture_stack);
            $this->capture_count--;
            if (isset($assign)) {
                $_template->assign($assign, ob_get_contents());
            }
            if (isset($append)) {
                $_template->append($append, ob_get_contents());
            }
            $this->named_buffer[$buffer] = ob_get_clean();
        } else {
            $this->error($_template);
        }
    }
    /**
     * Error exception on not matching {capture}{/capture}
     *
     *
     * @throws \Smarty\Exception
     */
    public function error(Template $_template)
    {
        throw new \Smarty\Exception("Not matching {capture}{/capture} in '{$_template->template_resource}'");
    }
    /**
     * Return content of named capture buffer by key or as array
     *
     * @param string|null $name
     * @return string|string[]|null
     */
    public function get_buffer(Template $_template, $name = null)
    {
        if (isset($name)) {
            return $this->named_buffer[$name] ?? null;
        }
        return $this->named_buffer;
    }
    /**
     * End render callback
     *
     *
     * @throws \Smarty\Exception
     */
    public function end_render(Template $_template): void
    {
        if ($this->capture_count) {
            $this->error($_template);
        } else {
            $this->capture_count = array_pop($this->count_stack);
        }
    }
}