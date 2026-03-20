<?php

declare (strict_types=1);
namespace Smarty\Runtime;

use Smarty\Exception;
class Default_Plugin_Handler_Runtime
{
    /**
     * @var callable
     */
    private $default_plugin_handler;
    public function __construct(?callable $default_plugin_handler = null)
    {
        $this->default_plugin_handler = $default_plugin_handler;
    }
    public function has_plugin($tag, $plugin_type): bool
    {
        if ($this->default_plugin_handler === null) {
            return false;
        }
        $callback = null;
        // these are not used here
        $script = null;
        $cacheable = null;
        return \call_user_func_array($this->default_plugin_handler, [
            $tag,
            $plugin_type,
            null,
            // This used to pass $this->template, but this parameter has been removed in 5.0
            &$callback,
            &$script,
            &$cacheable,
        ]) && $callback;
    }
    /**
     * @throws Exception
     */
    public function get_callback($tag, $plugin_type)
    {
        if ($this->default_plugin_handler === null) {
            return false;
        }
        $callback = null;
        // these are not used here
        $script = null;
        $cacheable = null;
        if (\call_user_func_array($this->default_plugin_handler, [
            $tag,
            $plugin_type,
            null,
            // This used to pass $this->template, but this parameter has been removed in 5.0
            &$callback,
            &$script,
            &$cacheable,
        ]) && $callback) {
            return $callback;
        }
        throw new Exception("Default plugin handler: Returned callback for '{$tag}' not callable at runtime");
    }
}