<?php

declare (strict_types=1);
namespace Smarty\Extension;

use Smarty\Block_Handler\Block_Plugin_Wrapper;
use Smarty\Compile\Compiler_Interface;
use Smarty\Compile\Modifier\Bc_Plugin_Wrapper as ModifierCompilerPluginWrapper;
use Smarty\Compile\Tag\Bc_Plugin_Wrapper as TagPluginWrapper;
use Smarty\Filter\Filter_Plugin_Wrapper;
use Smarty\Function_Handler\Bc_Plugin_Wrapper as FunctionPluginWrapper;
class Bc_Plugins_Adapter extends Base
{
    /**
     * @var \Smarty\Smarty
     */
    private $smarty;
    public function __construct(\Smarty\Smarty $smarty)
    {
        $this->smarty = $smarty;
    }
    private function find_plugin(string $type, string $name): ?array
    {
        if (null !== $plugin = $this->smarty->get_registered_plugin($type, $name)) {
            return $plugin;
        }
        return null;
    }
    public function get_tag_compiler(string $tag): ?\Smarty\Compile\Compiler_Interface
    {
        $plugin = $this->find_plugin(\Smarty\Smarty::PLUGIN_COMPILER, $tag);
        if ($plugin === null) {
            return null;
        }
        if (is_callable($plugin[0])) {
            $callback = $plugin[0];
            $cacheable = (bool) ($plugin[1] ?? true);
            return new Tag_Plugin_Wrapper($callback, $cacheable);
        }
        if (class_exists($plugin[0])) {
            $compiler = new $plugin[0]();
            if ($compiler instanceof Compiler_Interface) {
                return $compiler;
            }
        }
        return null;
    }
    public function get_function_handler(string $function_name): ?\Smarty\Function_Handler\Function_Handler_Interface
    {
        $plugin = $this->find_plugin(\Smarty\Smarty::PLUGIN_FUNCTION, $function_name);
        if ($plugin === null) {
            return null;
        }
        $callback = $plugin[0];
        $cacheable = (bool) ($plugin[1] ?? true);
        return new Function_Plugin_Wrapper($callback, $cacheable);
    }
    public function get_block_handler(string $block_tag_name): ?\Smarty\Block_Handler\Block_Handler_Interface
    {
        $plugin = $this->find_plugin(\Smarty\Smarty::PLUGIN_BLOCK, $block_tag_name);
        if ($plugin === null) {
            return null;
        }
        $callback = $plugin[0];
        $cacheable = (bool) ($plugin[1] ?? true);
        return new Block_Plugin_Wrapper($callback, $cacheable);
    }
    public function get_modifier_callback(string $modifier_name)
    {
        $plugin = $this->find_plugin(\Smarty\Smarty::PLUGIN_MODIFIER, $modifier_name);
        if ($plugin === null) {
            return null;
        }
        return $plugin[0];
    }
    public function get_modifier_compiler(string $modifier): ?\Smarty\Compile\Modifier\Modifier_Compiler_Interface
    {
        $plugin = $this->find_plugin(\Smarty\Smarty::PLUGIN_MODIFIERCOMPILER, $modifier);
        if ($plugin === null) {
            return null;
        }
        $callback = $plugin[0];
        return new Modifier_Compiler_Plugin_Wrapper($callback);
    }
    /**
     * @var array
     */
    private $pre_filters = [];
    public function get_pre_filters(): array
    {
        return $this->pre_filters;
    }
    public function add_pre_filter(\Smarty\Filter\Filter_Interface $filter): void
    {
        $this->pre_filters[] = $filter;
    }
    public function add_callable_as_pre_filter(callable $callable, ?string $name = null): void
    {
        if ($name === null) {
            $this->pre_filters[] = new Filter_Plugin_Wrapper($callable);
        } else {
            $this->pre_filters[$name] = new Filter_Plugin_Wrapper($callable);
        }
    }
    public function remove_prefilter(string $name): void
    {
        unset($this->pre_filters[$name]);
    }
    /**
     * @var array
     */
    private $post_filters = [];
    public function get_post_filters(): array
    {
        return $this->post_filters;
    }
    public function add_post_filter(\Smarty\Filter\Filter_Interface $filter): void
    {
        $this->post_filters[] = $filter;
    }
    public function add_callable_as_post_filter(callable $callable, ?string $name = null): void
    {
        if ($name === null) {
            $this->post_filters[] = new Filter_Plugin_Wrapper($callable);
        } else {
            $this->post_filters[$name] = new Filter_Plugin_Wrapper($callable);
        }
    }
    public function remove_post_filter(string $name): void
    {
        unset($this->post_filters[$name]);
    }
    /**
     * @var array
     */
    private $output_filters = [];
    public function get_output_filters(): array
    {
        return $this->output_filters;
    }
    public function add_output_filter(\Smarty\Filter\Filter_Interface $filter): void
    {
        $this->output_filters[] = $filter;
    }
    public function add_callable_as_output_filter(callable $callable, ?string $name = null): void
    {
        if ($name === null) {
            $this->output_filters[] = new Filter_Plugin_Wrapper($callable);
        } else {
            $this->output_filters[$name] = new Filter_Plugin_Wrapper($callable);
        }
    }
    public function remove_output_filter(string $name): void
    {
        unset($this->output_filters[$name]);
    }
    public function load_plugins_from_dir(string $path): void
    {
        foreach (['function', 'modifier', 'block', 'compiler', 'prefilter', 'postfilter', 'outputfilter', 'modifiercompiler'] as $type) {
            foreach (glob($path . $type . '.?*.php') as $filename) {
                $plugin_name = $this->get_plugin_name_from_filename($filename);
                if ($plugin_name !== null) {
                    require_once $filename;
                    $function_or_class_name = 'smarty_' . $type . '_' . $plugin_name;
                    if (function_exists($function_or_class_name) || class_exists($function_or_class_name)) {
                        $this->smarty->register_plugin($type, $plugin_name, $function_or_class_name, true);
                    }
                }
            }
        }
        $type = 'resource';
        foreach (glob($path . $type . '.?*.php') as $filename) {
            $plugin_name = $this->get_plugin_name_from_filename($filename);
            if ($plugin_name !== null) {
                require_once $filename;
                if (class_exists($class_name = 'smarty_' . $type . '_' . $plugin_name)) {
                    $this->smarty->register_resource($plugin_name, new $class_name());
                }
            }
        }
        $type = 'cacheresource';
        foreach (glob($path . $type . '.?*.php') as $filename) {
            $plugin_name = $this->get_plugin_name_from_filename($filename);
            if ($plugin_name !== null) {
                require_once $filename;
                if (class_exists($class_name = 'smarty_' . $type . '_' . $plugin_name)) {
                    $this->smarty->register_cache_resource($plugin_name, new $class_name());
                }
            }
        }
    }
    /**
     * @param $filename
     */
    private function get_plugin_name_from_filename(string $filename): ?string
    {
        if (!preg_match('/.*\.([a-z_A-Z0-9]+)\.php$/', $filename, $matches)) {
            return null;
        }
        return $matches[1];
    }
}