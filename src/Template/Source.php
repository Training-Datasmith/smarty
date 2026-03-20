<?php

declare (strict_types=1);
namespace Smarty\Template;

use Smarty\Exception;
use Smarty\Resource\File_Plugin;
use Smarty\Smarty;
use Smarty\Template;
/**
 * Meta-data Container for Template source files
 * @author     Rodney Rehm
 */
class Source
{
    /**
     * Unique Template ID
     *
     * @var string|null
     */
    public $uid;
    /**
     * Template Resource (\Smarty\Template::$template_resource)
     *
     * @var string
     */
    public $resource;
    /**
     * Resource Type
     *
     * @var string
     */
    public $type;
    /**
     * Resource Name
     *
     * @var string
     */
    public $name;
    /**
     * Source Timestamp
     *
     * @var int
     */
    public $timestamp;
    /**
     * Source Existence
     *
     * @var boolean
     */
    public $exists = false;
    /**
     * Source File Base name
     *
     * @var string
     */
    public $basename;
    /**
     * The Components an extended template is made of
     *
     * @var \Smarty\Template\Source[]
     */
    public $components;
    /**
     * Resource Handler
     *
     * @var \Smarty\Resource\BasePlugin
     */
    public $handler;
    /**
     * Smarty instance
     *
     * @var Smarty
     */
    protected $smarty;
    /**
     * Resource is source
     *
     * @var bool
     */
    public $is_config = false;
    /**
     * Template source content eventually set by default handler
     *
     * @var string
     */
    public $content;
    /**
     * @var array
     */
    protected static $_incompatible_resources = [];
    /**
     * create Source Object container
     *
     * @param Smarty $smarty Smarty instance this source object belongs to
     * @param string $resource full template_resource
     * @param string $type type of resource
     * @param string $name resource name
     *
     * @throws   \Smarty\Exception
     * @internal param \Smarty\Resource\Base $handler Resource Handler this source object communicates with
     */
    public function __construct(Smarty $smarty, string $type, string $name)
    {
        $this->handler = \Smarty\Resource\Base_Plugin::load($smarty, $type);
        $this->smarty = $smarty;
        $this->resource = $type . ':' . $name;
        $this->type = $type;
        $this->name = $name;
    }
    /**
     * initialize Source Object for given resource
     * Either [$_template] or [$smarty, $template_resource] must be specified
     *
     * @param Template|null $_template template object
     * @param Smarty|null $smarty smarty object
     * @param null $template_resource resource identifier
     *
     * @return Source Source Object
     * @throws Exception
     */
    public static function load(?Template $_template = null, ?Smarty $smarty = null, $template_resource = null): self
    {
        if ($_template) {
            $smarty = $_template->get_smarty();
            $template_resource = $_template->template_resource;
        }
        if (empty($template_resource)) {
            throw new Exception('Source: Missing  name');
        }
        // parse resource_name, load resource handler, identify unique resource name
        if (preg_match('/^([A-Za-z0-9_\-]{2,}):([\s\S]*)$/', $template_resource, $match)) {
            $type = $match[1];
            $name = $match[2];
        } else {
            // no resource given, use default
            // or single character before the colon is not a resource type, but part of the filepath
            $type = $smarty->default_resource_type;
            $name = $template_resource;
        }
        if (isset(self::$_incompatible_resources[$type])) {
            throw new Exception("Unable to use resource '{$type}' for " . __METHOD__);
        }
        // create new source object
        $source = new static($smarty, $type, $name);
        $source->handler->populate($source, $_template);
        if (!$source->exists && static::get_default_handler_func($smarty)) {
            $source->_get_default_template(static::get_default_handler_func($smarty));
            $source->handler->populate($source, $_template);
        }
        return $source;
    }
    protected static function get_default_handler_func(Smarty $smarty)
    {
        return $smarty->default_template_handler_func;
    }
    /**
     * Get source time stamp
     *
     * @return int
     */
    public function get_time_stamp()
    {
        if (!isset($this->timestamp)) {
            $this->handler->populate_timestamp($this);
        }
        return $this->timestamp;
    }
    /**
     * Get source content
     *
     * @return string
     * @throws \Smarty\Exception
     */
    public function get_content()
    {
        return $this->content ?? $this->handler->get_content($this);
    }
    /**
     * get default content from template or config resource handler
     *
     * @throws \Smarty\Exception
     */
    public function _get_default_template($default_handler): void
    {
        $_content = $_timestamp = null;
        $_return = \call_user_func_array($default_handler, [$this->type, $this->name, &$_content, &$_timestamp, $this->smarty]);
        if (is_string($_return)) {
            $this->exists = is_file($_return);
            if ($this->exists) {
                $this->timestamp = filemtime($_return);
            } else {
                throw new Exception('Default handler: Unable to load ' . "default file '{$_return}' for '{$this->type}:{$this->name}'");
            }
            $this->name = $_return;
            $this->uid = sha1($_return);
        } elseif ($_return === true) {
            $this->content = $_content;
            $this->exists = true;
            $this->uid = $this->name = sha1($_content);
            $this->handler = \Smarty\Resource\Base_Plugin::load($this->smarty, 'eval');
        } else {
            $this->exists = false;
            throw new Exception('Default handler: No ' . ($this->is_config ? 'config' : 'template') . " default content for '{$this->type}:{$this->name}'");
        }
    }
    public function create_compiler(): \Smarty\Compiler\Base_Compiler
    {
        return new \Smarty\Compiler\Template($this->smarty);
    }
    public function get_smarty()
    {
        return $this->smarty;
    }
    /**
     * Determine basename for compiled filename
     *
     * @return string                 resource's basename
     */
    public function get_basename()
    {
        return $this->handler->get_basename($this);
    }
    /**
     * Return source name
     * e.g.: 'sub/index.tpl'
     */
    public function get_resource_name(): string
    {
        return (string) $this->name;
    }
    /**
     * Return source name, including the type prefix.
     * e.g.: 'file:sub/index.tpl'
     */
    public function get_full_resource_name(): string
    {
        return $this->type . ':' . $this->name;
    }
    public function get_filepath(): ?string
    {
        if ($this->handler instanceof File_Plugin) {
            return $this->handler->get_file_path($this->name, $this->smarty, $this->is_config);
        }
        return null;
    }
    public function is_config(): bool
    {
        return $this->is_config;
    }
}