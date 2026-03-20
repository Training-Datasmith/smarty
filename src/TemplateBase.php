<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Smarty Template  Base
 * This file contains the basic shared methods for template handling
 *
 * @author     Uwe Tews
 */
namespace Smarty;

/**
 * Class with shared smarty/template methods
 */
abstract class Template_Base extends Data
{
    /**
     * Set this if you want different sets of cache files for the same
     * templates.
     *
     * @var string
     */
    public $cache_id;
    /**
     * Set this if you want different sets of compiled files for the same
     * templates.
     *
     * @var string
     */
    public $compile_id;
    /**
     * caching enabled
     *
     * @var int
     */
    public $caching = \Smarty\Smarty::CACHING_OFF;
    /**
     * check template for modifications?
     *
     * @var int
     */
    public $compile_check = \Smarty\Smarty::COMPILECHECK_ON;
    /**
     * cache lifetime in seconds
     *
     * @var int
     */
    public $cache_lifetime = 3600;
    /**
     * Array of source information for known template functions
     *
     * @var array
     */
    public $tpl_functions = [];
    /**
     * @var Debug
     */
    private $debug;
    /**
     * Registers object to be used in templates
     *
     * @param string $object_name
     * @param object $object the referenced PHP object to register
     * @param array $allowed_methods_properties list of allowed methods (empty = all)
     * @param bool $format smarty argument format, else traditional
     * @param array $block_methods list of block-methods
     *
     * @return static
     * @throws \Smarty\Exception
     *
     * @api  Smarty::registerObject()
     */
    public function register_object($object_name, $object, $allowed_methods_properties = [], $format = true, $block_methods = [])
    {
        $smarty = $this->get_smarty();
        // test if allowed methods callable
        if (!empty($allowed_methods_properties)) {
            foreach ((array) $allowed_methods_properties as $method) {
                if (!is_callable([$object, $method]) && !property_exists($object, $method)) {
                    throw new Exception("Undefined method or property '{$method}' in registered object");
                }
            }
        }
        // test if block methods callable
        if (!empty($block_methods)) {
            foreach ((array) $block_methods as $method) {
                if (!is_callable([$object, $method])) {
                    throw new Exception("Undefined method '{$method}' in registered object");
                }
            }
        }
        // register the object
        $smarty->registered_objects[$object_name] = [$object, (array) $allowed_methods_properties, (bool) $format, (array) $block_methods];
        return $this;
    }
    /**
     * Registers plugin to be used in templates
     *
     * @param string $object_name name of object
     *
     * @return static
     * @api  Smarty::unregisterObject()
     *
     */
    public function unregister_object($object_name)
    {
        $smarty = $this->get_smarty();
        if (isset($smarty->registered_objects[$object_name])) {
            unset($smarty->registered_objects[$object_name]);
        }
        return $this;
    }
    public function get_compile_check(): int
    {
        return $this->compile_check;
    }
    /**
     * @param int $compile_check
     */
    public function set_compile_check($compile_check): void
    {
        $this->compile_check = (int) $compile_check;
    }
    /**
     * @param int $caching
     */
    public function set_caching($caching): void
    {
        $this->caching = (int) $caching;
    }
    /**
     * @param int $cache_lifetime
     */
    public function set_cache_lifetime($cache_lifetime): void
    {
        $this->cache_lifetime = $cache_lifetime;
    }
    /**
     * @param string $compile_id
     */
    public function set_compile_id($compile_id): void
    {
        $this->compile_id = $compile_id;
    }
    /**
     * @param string $cache_id
     */
    public function set_cache_id($cache_id): void
    {
        $this->cache_id = $cache_id;
    }
    /**
     * creates a data object
     *
     * @param Data|null $parent next higher level of Smarty
     *                                                                                     variables
     * @param null $name optional data block name
     *
     * @return Data data object
     * @throws Exception
     * @api  Smarty::createData()
     *
     */
    public function create_data(?Data $parent = null, $name = null)
    {
        /* @var Smarty $smarty */
        $smarty = $this->get_smarty();
        $data_obj = new Data($parent, $smarty, $name);
        if ($smarty->debugging) {
            $smarty->get_debug()->register_data($data_obj);
        }
        return $data_obj;
    }
    /**
     * return name of debugging template
     *
     * @return string
     * @api Smarty::getDebugTemplate()
     *
     */
    public function get_debug_template()
    {
        $smarty = $this->get_smarty();
        return $smarty->debug_tpl;
    }
    public function get_debug(): Debug
    {
        if (!isset($this->debug)) {
            $this->debug = new \Smarty\Debug();
        }
        return $this->debug;
    }
    /**
     * return a reference to a registered object
     *
     * @param string $object_name object name
     *
     * @return object
     * @throws \Smarty\Exception if no such object is found
     *
     * @api  Smarty::getRegisteredObject()
     */
    public function get_registered_object($object_name)
    {
        $smarty = $this->get_smarty();
        if (!isset($smarty->registered_objects[$object_name])) {
            throw new Exception("'{$object_name}' is not a registered object");
        }
        if (!is_object($smarty->registered_objects[$object_name][0])) {
            throw new Exception("registered '{$object_name}' is not an object");
        }
        return $smarty->registered_objects[$object_name][0];
    }
    /**
     * Get literals
     *
     * @return array list of literals
     * @api Smarty::getLiterals()
     *
     */
    public function get_literals()
    {
        $smarty = $this->get_smarty();
        return (array) $smarty->literals;
    }
    /**
     * Add literals
     *
     * @param array|string $literals literal or list of literals
     *                                                                                  to addto add
     *
     * @return static
     * @throws \Smarty\Exception
     * @api Smarty::addLiterals()
     *
     */
    public function add_literals($literals = null)
    {
        if (isset($literals)) {
            $this->_set_literals($this->get_smarty(), (array) $literals);
        }
        return $this;
    }
    /**
     * Set literals
     *
     * @param array|string $literals literal or list of literals
     *                                                                                  to setto set
     *
     * @return static
     * @throws \Smarty\Exception
     * @api Smarty::setLiterals()
     *
     */
    public function set_literals($literals = null)
    {
        $smarty = $this->get_smarty();
        $smarty->literals = [];
        if (!empty($literals)) {
            $this->_set_literals($smarty, (array) $literals);
        }
        return $this;
    }
    /**
     * common setter for literals for easier handling of duplicates the
     * Smarty::$literals array gets filled with identical key values
     *
     *
     * @throws \Smarty\Exception
     */
    private function _set_literals(Smarty $smarty, array $literals): void
    {
        $literals = array_combine($literals, $literals);
        $error = isset($literals[$smarty->get_left_delimiter()]) ? [$smarty->get_left_delimiter()] : [];
        if (isset($literals[$smarty->get_right_delimiter()])) {
            $error[] = $smarty->get_right_delimiter();
        }
        if (!empty($error)) {
            throw new Exception('User defined literal(s) "' . $error . '" may not be identical with left or right delimiter');
        }
        $smarty->literals = array_merge((array) $smarty->literals, $literals);
    }
    /**
     * Registers static classes to be used in templates
     *
     * @param string $class_name
     * @param string $class_impl the referenced PHP class to
     *                                                                                    register
     *
     * @return static
     * @throws \Smarty\Exception
     * @api  Smarty::registerClass()
     *
     */
    public function register_class($class_name, $class_impl)
    {
        $smarty = $this->get_smarty();
        // test if exists
        if (!class_exists($class_impl)) {
            throw new Exception("Undefined class '{$class_impl}' in register template class");
        }
        // register the class
        $smarty->registered_classes[$class_name] = $class_impl;
        return $this;
    }
    /**
     * Register config default handler
     *
     * @param callable $callback class/method name
     *
     * @return static
     * @throws Exception              if $callback is not callable
     * @api Smarty::registerDefaultConfigHandler()
     *
     */
    public function register_default_config_handler($callback)
    {
        $smarty = $this->get_smarty();
        if (is_callable($callback)) {
            $smarty->default_config_handler_func = $callback;
        } else {
            throw new Exception('Default config handler not callable');
        }
        return $this;
    }
    /**
     * Register template default handler
     *
     * @param callable $callback class/method name
     *
     * @return static
     * @throws Exception              if $callback is not callable
     * @api Smarty::registerDefaultTemplateHandler()
     *
     */
    public function register_default_template_handler($callback)
    {
        $smarty = $this->get_smarty();
        if (is_callable($callback)) {
            $smarty->default_template_handler_func = $callback;
        } else {
            throw new Exception('Default template handler not callable');
        }
        return $this;
    }
    /**
     * Registers a resource to fetch a template
     *
     * @param string $name name of resource type
     * @param \Smarty\Resource\BasePlugin $resource_handler instance of Smarty\Resource\BasePlugin
     *
     * @return static
     *
     * @api  Smarty::registerResource()
     */
    public function register_resource($name, \Smarty\Resource\Base_Plugin $resource_handler)
    {
        $smarty = $this->get_smarty();
        $smarty->registered_resources[$name] = $resource_handler;
        return $this;
    }
    /**
     * Unregisters a resource to fetch a template
     *
     * @param string $type name of resource type
     *
     * @return static
     * @api  Smarty::unregisterResource()
     *
     */
    public function unregister_resource($type)
    {
        $smarty = $this->get_smarty();
        if (isset($smarty->registered_resources[$type])) {
            unset($smarty->registered_resources[$type]);
        }
        return $this;
    }
    /**
     * set the debug template
     *
     * @param string $tpl_name
     *
     * @return static
     * @throws Exception if file is not readable
     * @api Smarty::setDebugTemplate()
     *
     */
    public function set_debug_template($tpl_name)
    {
        $smarty = $this->get_smarty();
        if (!is_readable($tpl_name)) {
            throw new Exception("Unknown file '{$tpl_name}'");
        }
        $smarty->debug_tpl = $tpl_name;
        return $this;
    }
}