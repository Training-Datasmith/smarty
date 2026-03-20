<?php

declare (strict_types=1);
namespace Smarty\Resource;

use Smarty\Exception;
use Smarty\Smarty;
use Smarty\Template;
use Smarty\Template\Source;
/**
 * Smarty Resource Plugin
 * Base implementation for resource plugins
 * @author     Rodney Rehm
 */
abstract class Base_Plugin
{
    /**
     * resource types provided by the core
     *
     * @var array
     */
    public static $sysplugins = ['file' => File_Plugin::class, 'string' => String_Plugin::class, 'extends' => Extends_Plugin::class, 'stream' => Stream_Plugin::class, 'eval' => String_Eval::class];
    /**
     * Source must be recompiled on every occasion
     *
     * @var boolean
     */
    public $recompiled = false;
    /**
     * Flag if resource does allow compilation
     */
    public function supports_compiled_templates(): bool
    {
        return true;
    }
    /**
     * Check if resource must check time stamps when loading compiled or cached templates.
     * Resources like 'extends' which use source components my disable timestamp checks on own resource.
     * @return bool
     */
    public function check_timestamps()
    {
        return true;
    }
    /**
     * Load Resource Handler
     *
     * @param Smarty $smarty smarty object
     * @param string $type name of the resource
     *
     * @return BasePlugin Resource Handler
     * @throws Exception
     */
    public static function load(Smarty $smarty, $type)
    {
        // try smarty's cache
        if (isset($smarty->_resource_handlers[$type])) {
            return $smarty->_resource_handlers[$type];
        }
        // try registered resource
        if (isset($smarty->registered_resources[$type])) {
            return $smarty->_resource_handlers[$type] = $smarty->registered_resources[$type];
        }
        // try sysplugins dir
        if (isset(self::$sysplugins[$type])) {
            $_resource_class = self::$sysplugins[$type];
            return $smarty->_resource_handlers[$type] = new $_resource_class();
        }
        // try plugins dir
        $_resource_class = 'Smarty_Resource_' . \smarty_ucfirst_ascii($type);
        if (class_exists($_resource_class, false)) {
            return $smarty->_resource_handlers[$type] = new $_resource_class();
        }
        // try streams
        $_known_stream = stream_get_wrappers();
        if (in_array($type, $_known_stream)) {
            // is known stream
            if (is_object($smarty->security_policy)) {
                $smarty->security_policy->is_trusted_stream($type);
            }
            return $smarty->_resource_handlers[$type] = new Stream_Plugin();
        }
        // TODO: try default_(template|config)_handler
        // give up
        throw new \Smarty\Exception("Unknown resource type '{$type}'");
    }
    /**
     * Load template's source into current template object
     *
     * @param Source $source source object
     *
     * @return string                 template source
     * @throws \Smarty\Exception        if source cannot be loaded
     */
    abstract public function get_content(Source $source);
    /**
     * populate Source Object with metadata from Resource
     *
     * @param Source $source source object
     * @param Template|null $_template template object
     */
    abstract public function populate(Source $source, ?\Smarty\Template $_template = null);
    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Source $source source object
     */
    public function populate_timestamp(Source $source): void
    {
        // intentionally left blank
    }
    /*
     * Check if resource must check time stamps when when loading complied or cached templates.
     * Resources like 'extends' which use source components my disable timestamp checks on own resource.
     *
     * @return bool
     */
    /**
     * Determine basename for compiled filename
     *
     * @param \Smarty\Template\Source $source source object
     *
     * @return string                 resource's basename
     */
    public function get_basename(\Smarty\Template\Source $source)
    {
        return basename(preg_replace('![^\w]+!', '_', $source->name));
    }
}