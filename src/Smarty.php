<?php

declare (strict_types=1);
namespace Smarty;

use Filesystem_Iterator;
use Recursive_Directory_Iterator;
use Recursive_Iterator_Iterator;
use Smarty\Cacheresource\File;
use Smarty\Extension\Base;
use Smarty\Extension\Bc_Plugins_Adapter;
use Smarty\Extension\Callback_Wrapper;
use Smarty\Extension\Core_Extension;
use Smarty\Extension\Default_Extension;
use Smarty\Extension\Extension_Interface;
use Smarty\Filter\Output\Trim_Whitespace;
use Smarty\Runtime\Capture_Runtime;
use Smarty\Runtime\Default_Plugin_Handler_Runtime;
use Smarty\Runtime\Foreach_Runtime;
use Smarty\Runtime\Inheritance_Runtime;
use Smarty\Runtime\Tpl_Function_Runtime;
/**
 * Project:     Smarty: the PHP compiling template engine
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * For questions, help, comments, discussion, etc., please join the
 * Smarty mailing list. Send a blank e-mail to
 * smarty-discussion-subscribe@googlegroups.com
 *
 * @author    Monte Ohrt <monte at ohrt dot com>
 * @author    Uwe Tews   <uwe dot tews at gmail dot com>
 * @author    Rodney Rehm
 * @author    Simon Wisselink
 */
/**
 * This is the main Smarty class
 */
class Smarty extends \Smarty\Template_Base
{
    /**
     * smarty version
     */
    public const SMARTY_VERSION = '5.8.0';
    /**
     * define caching modes
     */
    public const CACHING_OFF = 0;
    public const CACHING_LIFETIME_CURRENT = 1;
    public const CACHING_LIFETIME_SAVED = 2;
    /**
     * define constant for clearing cache files be saved expiration dates
     */
    public const CLEAR_EXPIRED = -1;
    /**
     * define compile check modes
     */
    public const COMPILECHECK_OFF = 0;
    public const COMPILECHECK_ON = 1;
    /**
     * filter types
     */
    public const FILTER_POST = 'post';
    public const FILTER_PRE = 'pre';
    public const FILTER_OUTPUT = 'output';
    public const FILTER_VARIABLE = 'variable';
    /**
     * plugin types
     */
    public const PLUGIN_FUNCTION = 'function';
    public const PLUGIN_BLOCK = 'block';
    public const PLUGIN_COMPILER = 'compiler';
    public const PLUGIN_MODIFIER = 'modifier';
    public const PLUGIN_MODIFIERCOMPILER = 'modifiercompiler';
    /**
     * The character set to adhere to (defaults to "UTF-8")
     */
    public static $_CHARSET = 'UTF-8';
    /**
     * The date format to be used internally
     * (accepts date() and strftime())
     */
    public static $_DATE_FORMAT = '%b %e, %Y';
    /**
     * Flag denoting if PCRE should run in UTF-8 mode
     */
    public static $_UTF8_MODIFIER = 'u';
    /**
     * Flag denoting if operating system is windows
     */
    public static $_IS_WINDOWS = false;
    /**
     * auto literal on delimiters with whitespace
     *
     * @var boolean
     */
    public $auto_literal = true;
    /**
     * display error on not assigned variables
     *
     * @var boolean
     */
    public $error_unassigned = false;
    /**
     * flag if template_dir is normalized
     *
     * @var bool
     */
    public $_template_dir_normalized = false;
    /**
     * joined template directory string used in cache keys
     *
     * @var string
     */
    public $_joined_template_dir;
    /**
     * flag if config_dir is normalized
     *
     * @var bool
     */
    public $_config_dir_normalized = false;
    /**
     * joined config directory string used in cache keys
     *
     * @var string
     */
    public $_joined_config_dir;
    /**
     * default template handler
     *
     * @var callable
     */
    public $default_template_handler_func;
    /**
     * default config handler
     *
     * @var callable
     */
    public $default_config_handler_func;
    /**
     * default plugin handler
     *
     * @var callable
     */
    private $default_plugin_handler_func;
    /**
     * flag if template_dir is normalized
     *
     * @var bool
     */
    public $_compile_dir_normalized = false;
    /**
     * flag if template_dir is normalized
     *
     * @var bool
     */
    public $_cache_dir_normalized = false;
    /**
     * force template compiling?
     *
     * @var boolean
     */
    public $force_compile = false;
    /**
     * use sub dirs for compiled/cached files?
     *
     * @var boolean
     */
    public $use_sub_dirs = false;
    /**
     * merge compiled includes
     *
     * @var boolean
     */
    public $merge_compiled_includes = false;
    /**
     * force cache file creation
     *
     * @var boolean
     */
    public $force_cache = false;
    /**
     * template left-delimiter
     *
     * @var string
     */
    private $left_delimiter = '{';
    /**
     * template right-delimiter
     *
     * @var string
     */
    private $right_delimiter = '}';
    /**
     * array of strings which shall be treated as literal by compiler
     *
     * @var array string
     */
    public $literals = [];
    /**
     * class name
     * This should be instance of \Smarty\Security.
     *
     * @var string
     * @see \Smarty\Security
     */
    public $security_class = \Smarty\Security::class;
    /**
     * implementation of security class
     *
     * @var \Smarty\Security
     */
    public $security_policy;
    /**
     * debug mode
     * Setting this to true enables the debug-console. Setting it to 2 enables individual Debug Console window by
     * template name.
     *
     * @var boolean|int
     */
    public $debugging = false;
    /**
     * This determines if debugging is enable-able from the browser.
     * <ul>
     *  <li>NONE => no debugging control allowed</li>
     *  <li>URL => enable debugging when SMARTY_DEBUG is found in the URL.</li>
     * </ul>
     *
     * @var string
     */
    public $debugging_ctrl = 'NONE';
    /**
     * Name of debugging URL-param.
     * Only used when $debugging_ctrl is set to 'URL'.
     * The name of the URL-parameter that activates debugging.
     *
     * @var string
     */
    public $smarty_debug_id = 'SMARTY_DEBUG';
    /**
     * Path of debug template.
     *
     * @var string
     */
    public $debug_tpl;
    /**
     * When set, smarty uses this value as error_reporting-level.
     *
     * @var int
     */
    public $error_reporting;
    /**
     * Controls whether variables with the same name overwrite each other.
     *
     * @var boolean
     */
    public $config_overwrite = true;
    /**
     * Controls whether config values of on/true/yes and off/false/no get converted to boolean.
     *
     * @var boolean
     */
    public $config_booleanize = true;
    /**
     * Controls whether hidden config sections/vars are read from the file.
     *
     * @var boolean
     */
    public $config_read_hidden = false;
    /**
     * locking concurrent compiles
     *
     * @var boolean
     */
    public $compile_locking = true;
    /**
     * Controls whether cache resources should use locking mechanism
     *
     * @var boolean
     */
    public $cache_locking = false;
    /**
     * seconds to wait for acquiring a lock before ignoring the write lock
     *
     * @var float
     */
    public $locking_timeout = 10;
    /**
     * resource type used if none given
     * Must be a valid key of $registered_resources.
     *
     * @var string
     */
    public $default_resource_type = 'file';
    /**
     * cache resource
     * Must be a subclass of \Smarty\Cacheresource\Base
     *
     * @var \Smarty\Cacheresource\Base
     */
    private $cache_resource;
    /**
     * config type
     *
     * @var string
     */
    public $default_config_type = 'file';
    /**
     * check If-Modified-Since headers
     *
     * @var boolean
     */
    public $cache_modified_check = false;
    /**
     * registered plugins
     *
     * @var array
     */
    public $registered_plugins = [];
    /**
     * registered objects
     *
     * @var array
     */
    public $registered_objects = [];
    /**
     * registered classes
     *
     * @var array
     */
    public $registered_classes = [];
    /**
     * registered resources
     *
     * @var array
     */
    public $registered_resources = [];
    /**
     * registered cache resources
     *
     * @var array
     * @deprecated since 5.0
     */
    private $registered_cache_resources = [];
    /**
     * default modifier
     *
     * @var array
     */
    public $default_modifiers = [];
    /**
     * autoescape variable output
     *
     * @var boolean
     */
    public $escape_html = false;
    /**
     * start time for execution time calculation
     *
     * @var int
     */
    public $start_time = 0;
    /**
     * internal flag to enable parser debugging
     *
     * @var bool
     */
    public $_parserdebug = false;
    /**
     * Debug object
     *
     * @var \Smarty\Debug
     */
    public $_debug;
    /**
     * template directory
     *
     * @var array
     */
    protected $template_dir = ['./templates/'];
    /**
     * flags for normalized template directory entries
     *
     * @var array
     */
    protected $_processed_template_dir = [];
    /**
     * config directory
     *
     * @var array
     */
    protected $config_dir = ['./configs/'];
    /**
     * flags for normalized template directory entries
     *
     * @var array
     */
    protected $_processed_config_dir = [];
    /**
     * compile directory
     *
     * @var string
     */
    protected $compile_dir = './templates_c/';
    /**
     * cache directory
     *
     * @var string
     */
    protected $cache_dir = './cache/';
    /**
     * PHP7 Compatibility mode
     *
     * @var bool
     */
    private $is_muting_undefined_or_null_warnings = false;
    /**
     * Cache of loaded resource handlers.
     *
     * @var array
     */
    public $_resource_handlers = [];
    /**
     * Cache of loaded cacheresource handlers.
     *
     * @var array
     */
    public $_cacheresource_handlers = [];
    /**
     * List of extensions
     *
     * @var ExtensionInterface[]
     */
    private $extensions = [];
    /**
     * @var BCPluginsAdapter
     */
    private $bc_plugins_adapter;
    /**
     * Initialize new Smarty object
     */
    public function __construct()
    {
        $this->start_time = microtime(true);
        // Check if we're running on Windows
        \Smarty\Smarty::$_IS_WINDOWS = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        // let PCRE (preg_*) treat strings as ISO-8859-1 if we're not dealing with UTF-8
        if (\Smarty\Smarty::$_CHARSET !== 'UTF-8') {
            \Smarty\Smarty::$_UTF8_MODIFIER = '';
        }
        $this->bc_plugins_adapter = new Bc_Plugins_Adapter($this);
        $this->extensions[] = new Core_Extension();
        $this->extensions[] = new Default_Extension();
        $this->extensions[] = $this->bc_plugins_adapter;
        $this->cache_resource = new File();
    }
    /**
     * Load an additional extension.
     */
    public function add_extension(Extension_Interface $extension): void
    {
        $this->extensions[] = $extension;
    }
    /**
     * Returns all loaded extensions
     *
     * @return array|ExtensionInterface[]
     */
    public function get_extensions(): array
    {
        return $this->extensions;
    }
    /**
     * Replace the entire list extensions, allowing you to determine the exact order of the extensions.
     *
     * @param ExtensionInterface[] $extensions
     */
    public function set_extensions(array $extensions): void
    {
        $this->extensions = $extensions;
    }
    /**
     * Check if a template resource exists
     *
     * @param string $resource_name template name
     *
     * @return bool status
     * @throws \Smarty\Exception
     */
    public function template_exists($resource_name)
    {
        // create source object
        $source = Template\Source::load(null, $this, $resource_name);
        return $source->exists;
    }
    /**
     * Loads security class and enables security
     *
     * @param string|\Smarty\Security $security_class if a string is used, it must be class-name
     *
     * @return static                 current Smarty instance for chaining
     * @throws \Smarty\Exception
     */
    public function enable_security($security_class = null): self
    {
        \Smarty\Security::enable_security($this, $security_class);
        return $this;
    }
    /**
     * Disable security
     *
     * @return static current Smarty instance for chaining
     */
    public function disable_security(): self
    {
        $this->security_policy = null;
        return $this;
    }
    /**
     * Add template directory(s)
     *
     * @param string|array $template_dir directory(s) of template sources
     * @param string $key of the array element to assign the template dir to
     * @param bool $isConfig true for config_dir
     *
     * @return static current Smarty instance for chaining
     */
    public function add_template_dir($template_dir, $key = null, $is_config = false): self
    {
        if ($is_config) {
            $processed =& $this->_processed_config_dir;
            $dir =& $this->config_dir;
            $this->_config_dir_normalized = false;
        } else {
            $processed =& $this->_processed_template_dir;
            $dir =& $this->template_dir;
            $this->_template_dir_normalized = false;
        }
        if (is_array($template_dir)) {
            foreach ($template_dir as $k => $v) {
                if (is_int($k)) {
                    // indexes are not merged but appended
                    $dir[] = $v;
                } else {
                    // string indexes are overridden
                    $dir[$k] = $v;
                    unset($processed[$key]);
                }
            }
        } else if ($key !== null) {
            // override directory at specified index
            $dir[$key] = $template_dir;
            unset($processed[$key]);
        } else {
            // append new directory
            $dir[] = $template_dir;
        }
        return $this;
    }
    /**
     * Get template directories
     *
     * @param mixed $index index of directory to get, null to get all
     * @param bool $isConfig true for config_dir
     *
     * @return array|string list of template directories, or directory of $index
     */
    public function get_template_dir($index = null, $is_config = false)
    {
        if ($is_config) {
            $dir =& $this->config_dir;
        } else {
            $dir =& $this->template_dir;
        }
        if ($is_config ? !$this->_config_dir_normalized : !$this->_template_dir_normalized) {
            $this->_normalize_template_config($is_config);
        }
        if ($index !== null) {
            return $dir[$index] ?? null;
        }
        return $dir;
    }
    /**
     * Set template directory
     *
     * @param string|array $template_dir directory(s) of template sources
     * @param bool $isConfig true for config_dir
     *
     * @return static current Smarty instance for chaining
     */
    public function set_template_dir($template_dir, $is_config = false): self
    {
        if ($is_config) {
            $this->config_dir = [];
            $this->_processed_config_dir = [];
        } else {
            $this->template_dir = [];
            $this->_processed_template_dir = [];
        }
        $this->add_template_dir($template_dir, null, $is_config);
        return $this;
    }
    /**
     * Adds a template directory before any existing directoires
     *
     * @param string $new_template_dir directory of template sources
     * @param bool $is_config true for config_dir
     *
     * @return static current Smarty instance for chaining
     */
    public function prepend_template_dir($new_template_dir, $is_config = false): self
    {
        $current_template_dirs = $is_config ? $this->config_dir : $this->template_dir;
        array_unshift($current_template_dirs, $new_template_dir);
        $this->set_template_dir($current_template_dirs, $is_config);
        return $this;
    }
    /**
     * Add config directory(s)
     *
     * @param string|array $config_dir directory(s) of config sources
     * @param mixed $key key of the array element to assign the config dir to
     *
     * @return static current Smarty instance for chaining
     */
    public function add_config_dir($config_dir, $key = null)
    {
        return $this->add_template_dir($config_dir, $key, true);
    }
    /**
     * Get config directory
     *
     * @param mixed $index index of directory to get, null to get all
     *
     * @return array configuration directory
     */
    public function get_config_dir($index = null)
    {
        return $this->get_template_dir($index, true);
    }
    /**
     * Set config directory
     *
     * @param $config_dir
     *
     * @return static current Smarty instance for chaining
     */
    public function set_config_dir($config_dir)
    {
        return $this->set_template_dir($config_dir, true);
    }
    /**
     * Registers plugin to be used in templates
     *
     * @param string $type plugin type
     * @param string $name name of template tag
     * @param callable $callback PHP callback to register
     * @param bool $cacheable if true (default) this function is cache able
     *
     * @return $this
     * @throws \Smarty\Exception
     *
     * @api  Smarty::registerPlugin()
     */
    public function register_plugin($type, $name, $callback, $cacheable = true): self
    {
        if (isset($this->registered_plugins[$type][$name])) {
            throw new Exception("Plugin tag '{$name}' already registered");
        }
        if (!is_callable($callback) && !class_exists($callback)) {
            throw new Exception("Plugin '{$name}' not callable");
        }
        $this->registered_plugins[$type][$name] = [$callback, (bool) $cacheable];
        return $this;
    }
    /**
     * Returns plugin previously registered using ::registerPlugin as a numerical array as follows or null if not found:
     * [
     *  0 => the callback
     *  1 => (bool) $cacheable
     *  2 => (array) $cache_attr
     * ]
     *
     * @param string $type plugin type
     * @param string $name name of template tag
     *
     *
     * @api  Smarty::unregisterPlugin()
     */
    public function get_registered_plugin($type, $name): ?array
    {
        return $this->registered_plugins[$type][$name] ?? null;
    }
    /**
     * Unregisters plugin previously registered using ::registerPlugin
     *
     * @param string $type plugin type
     * @param string $name name of template tag
     *
     * @return $this
     *
     * @api  Smarty::unregisterPlugin()
     */
    public function unregister_plugin($type, $name): self
    {
        if (isset($this->registered_plugins[$type][$name])) {
            unset($this->registered_plugins[$type][$name]);
        }
        return $this;
    }
    /**
     * Adds directory of plugin files
     *
     * @param null|array|string $plugins_dir
     *
     * @return static current Smarty instance for chaining
     * @deprecated since 5.0
     */
    public function add_plugins_dir($plugins_dir): self
    {
        trigger_error('Using Smarty::addPluginsDir() to load plugins is deprecated and will be ' . 'removed in a future release. Use Smarty::addExtension() to add an extension or Smarty::registerPlugin to ' . 'quickly register a plugin using a callback function.', E_USER_DEPRECATED);
        foreach ((array) $plugins_dir as $v) {
            $path = $this->_realpath(rtrim($v ?? '', '/\\') . DIRECTORY_SEPARATOR, true);
            $this->bc_plugins_adapter->load_plugins_from_dir($path);
        }
        return $this;
    }
    /**
     * Get plugin directories
     *
     * @return array list of plugin directories
     * @deprecated since 5.0
     */
    public function get_plugins_dir(): array
    {
        trigger_error('Using Smarty::getPluginsDir() is deprecated and will be ' . 'removed in a future release. It will always return an empty array.', E_USER_DEPRECATED);
        return [];
    }
    /**
     * Set plugins directory
     *
     * @param string|array $plugins_dir directory(s) of plugins
     *
     * @return static current Smarty instance for chaining
     * @deprecated since 5.0
     */
    public function set_plugins_dir($plugins_dir)
    {
        trigger_error('Using Smarty::getPluginsDir() is deprecated and will be ' . 'removed in a future release. For now, it will remove the DefaultExtension from the extensions list and ' . 'proceed to call Smartyy::addPluginsDir..', E_USER_DEPRECATED);
        $this->extensions = array_filter($this->extensions, function (\Smarty\Extension\Extension_Interface $extension): bool {
            return !$extension instanceof Default_Extension;
        });
        return $this->add_plugins_dir($plugins_dir);
    }
    /**
     * Registers a default plugin handler
     *
     * @param callable $callback class/method name
     *
     * @return $this
     * @throws Exception              if $callback is not callable
     *
     * @api  Smarty::registerDefaultPluginHandler()
     *
     * @deprecated since 5.0
     */
    public function register_default_plugin_handler($callback): self
    {
        trigger_error('Using Smarty::registerDefaultPluginHandler() is deprecated and will be ' . 'removed in a future release. Please rewrite your plugin handler as an extension.', E_USER_DEPRECATED);
        if (is_callable($callback)) {
            $this->default_plugin_handler_func = $callback;
        } else {
            throw new Exception("Default plugin handler '{$callback}' not callable");
        }
        return $this;
    }
    /**
     * Get compiled directory
     *
     * @return string path to compiled templates
     */
    public function get_compile_dir()
    {
        if (!$this->_compile_dir_normalized) {
            $this->_normalize_dir('compile_dir', $this->compile_dir);
            $this->_compile_dir_normalized = true;
        }
        return $this->compile_dir;
    }
    /**
     *
     * @param string $compile_dir directory to store compiled templates in
     *
     * @return static current Smarty instance for chaining
     */
    public function set_compile_dir($compile_dir): self
    {
        $this->_normalize_dir('compile_dir', $compile_dir);
        $this->_compile_dir_normalized = true;
        return $this;
    }
    /**
     * Get cache directory
     *
     * @return string path of cache directory
     */
    public function get_cache_dir()
    {
        if (!$this->_cache_dir_normalized) {
            $this->_normalize_dir('cache_dir', $this->cache_dir);
            $this->_cache_dir_normalized = true;
        }
        return $this->cache_dir;
    }
    /**
     * Set cache directory
     *
     * @param string $cache_dir directory to store cached templates in
     *
     * @return static current Smarty instance for chaining
     */
    public function set_cache_dir($cache_dir): self
    {
        $this->_normalize_dir('cache_dir', $cache_dir);
        $this->_cache_dir_normalized = true;
        return $this;
    }
    private $templates = [];
    /**
     * Creates a template object
     *
     * @param string $template_name
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     * @param null $parent next higher level of Smarty variables
     *
     * @return Template template object
     * @throws Exception
     */
    public function create_template($template_name, $cache_id = null, $compile_id = null, $parent = null): Template
    {
        $data = [];
        // Shuffle params for backward compatibility: if 2nd param is an object, it's the parent
        if (is_object($cache_id)) {
            $parent = $cache_id;
            $cache_id = null;
        }
        // Shuffle params for backward compatibility: if 2nd param is an array, it's data
        if (is_array($cache_id)) {
            $data = $cache_id;
            $cache_id = null;
        }
        return $this->do_create_template($template_name, $cache_id, $compile_id, $parent, null, null, false, $data);
    }
    /**
     * Get unique template id
     *
     * @param string $resource_name
     * @param null|mixed $cache_id
     * @param null|mixed $compile_id
     *
     */
    private function generate_unique_template_id($resource_name, $cache_id = null, $compile_id = null, $caching = null): string
    {
        // defaults for optional params
        $cache_id = $cache_id ?? $this->cache_id;
        $compile_id = $compile_id ?? $this->compile_id;
        $caching = (int) ($caching ?? $this->caching);
        // Add default resource type to resource name if it is missing
        if (strpos($resource_name, ':') === false) {
            $resource_name = "{$this->default_resource_type}:{$resource_name}";
        }
        $_template_id = $resource_name . '#' . $cache_id . '#' . $compile_id . '#' . $caching;
        // hash very long IDs to prevent problems with filename length
        // do not hash shorter IDs, so they remain recognizable
        if (strlen($_template_id) > 150) {
            return sha1($_template_id);
        }
        return $_template_id;
    }
    /**
     * Normalize path
     *  - remove /./ and /../
     *  - make it absolute if required
     *
     * @param string $path file path
     * @param bool $realpath if true - convert to absolute
     *                         false - convert to relative
     *                         null - keep as it is but
     *                         remove /./ /../
     *
     * @return string
     */
    public function _realpath($path, $realpath = null)
    {
        $nds = ['/' => '\\', '\\' => '/'];
        preg_match('%^(?<root>(?:[[:alpha:]]:[\\\\/]|/|[\\\\]{2}[[:alpha:]]+|[[:print:]]{2,}:[/]{2}|[\\\\])?)(?<path>(.*))$%u', $path, $parts);
        $path = $parts['path'];
        if ($parts['root'] === '\\') {
            $parts['root'] = substr(getcwd(), 0, 2) . $parts['root'];
        } else if ($realpath !== null && !$parts['root']) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        }
        // normalize DIRECTORY_SEPARATOR
        $path = str_replace($nds[DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, $path);
        $parts['root'] = str_replace($nds[DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, $parts['root']);
        do {
            $path = preg_replace(['#[\\\\/]{2}#', '#[\\\\/][.][\\\\/]#', '#[\\\\/]([^\\\\/.]+)[\\\\/][.][.][\\\\/]#'], DIRECTORY_SEPARATOR, $path, -1, $count);
        } while ($count > 0);
        return $realpath !== false ? $parts['root'] . $path : str_ireplace(getcwd(), '.', $parts['root'] . $path);
    }
    /**
     * @param boolean $use_sub_dirs
     */
    public function set_use_sub_dirs($use_sub_dirs): void
    {
        $this->use_sub_dirs = $use_sub_dirs;
    }
    /**
     * @param int $error_reporting
     */
    public function set_error_reporting($error_reporting): void
    {
        $this->error_reporting = $error_reporting;
    }
    /**
     * @param boolean $escape_html
     */
    public function set_escape_html($escape_html): void
    {
        $this->escape_html = $escape_html;
    }
    /**
     * Return auto_literal flag
     *
     * @return boolean
     */
    public function get_auto_literal()
    {
        return $this->auto_literal;
    }
    /**
     * Set auto_literal flag
     *
     * @param boolean $auto_literal
     */
    public function set_auto_literal($auto_literal = true): void
    {
        $this->auto_literal = $auto_literal;
    }
    /**
     * @param boolean $force_compile
     */
    public function set_force_compile($force_compile): void
    {
        $this->force_compile = $force_compile;
    }
    /**
     * @param boolean $merge_compiled_includes
     */
    public function set_merge_compiled_includes($merge_compiled_includes): void
    {
        $this->merge_compiled_includes = $merge_compiled_includes;
    }
    /**
     * Get left delimiter
     *
     * @return string
     */
    public function get_left_delimiter()
    {
        return $this->left_delimiter;
    }
    /**
     * Set left delimiter
     *
     * @param string $left_delimiter
     */
    public function set_left_delimiter($left_delimiter): void
    {
        $this->left_delimiter = $left_delimiter;
    }
    /**
     * Get right delimiter
     *
     * @return string $right_delimiter
     */
    public function get_right_delimiter()
    {
        return $this->right_delimiter;
    }
    /**
     * Set right delimiter
     *
     * @param string
     */
    public function set_right_delimiter($right_delimiter): void
    {
        $this->right_delimiter = $right_delimiter;
    }
    /**
     * @param boolean $debugging
     */
    public function set_debugging($debugging): void
    {
        $this->debugging = $debugging;
    }
    /**
     * @param boolean $config_overwrite
     */
    public function set_config_overwrite($config_overwrite): void
    {
        $this->config_overwrite = $config_overwrite;
    }
    /**
     * @param boolean $config_booleanize
     */
    public function set_config_booleanize($config_booleanize): void
    {
        $this->config_booleanize = $config_booleanize;
    }
    /**
     * @param boolean $config_read_hidden
     */
    public function set_config_read_hidden($config_read_hidden): void
    {
        $this->config_read_hidden = $config_read_hidden;
    }
    /**
     * @param boolean $compile_locking
     */
    public function set_compile_locking($compile_locking): void
    {
        $this->compile_locking = $compile_locking;
    }
    /**
     * @param string $default_resource_type
     */
    public function set_default_resource_type($default_resource_type): void
    {
        $this->default_resource_type = $default_resource_type;
    }
    /**
     * Test install
     */
    public function test_install(&$errors = null): void
    {
        \Smarty\Test_Install::test_install($this, $errors);
    }
    /**
     * Get Smarty object
     *
     * @return static
     */
    public function get_smarty(): self
    {
        return $this;
    }
    /**
     * Normalize and set directory string
     *
     * @param string $dirName cache_dir or compile_dir
     * @param string $dir filepath of folder
     */
    private function _normalize_dir(string $dir_name, $dir): void
    {
        $this->{$dir_name} = $this->_realpath(rtrim($dir ?? '', '/\\') . DIRECTORY_SEPARATOR, true);
    }
    /**
     * Normalize template_dir or config_dir
     *
     * @param bool $isConfig true for config_dir
     */
    private function _normalize_template_config(bool $is_config): void
    {
        if ($is_config) {
            $processed =& $this->_processed_config_dir;
            $dir =& $this->config_dir;
        } else {
            $processed =& $this->_processed_template_dir;
            $dir =& $this->template_dir;
        }
        if (!is_array($dir)) {
            $dir = (array) $dir;
        }
        foreach ($dir as $k => $v) {
            if (!isset($processed[$k])) {
                $dir[$k] = $this->_realpath(rtrim($v ?? '', '/\\') . DIRECTORY_SEPARATOR, true);
                $processed[$k] = true;
            }
        }
        if ($is_config) {
            $this->_config_dir_normalized = true;
            $this->_joined_config_dir = join('#', $this->config_dir);
        } else {
            $this->_template_dir_normalized = true;
            $this->_joined_template_dir = join('#', $this->template_dir);
        }
    }
    /**
     * Mutes errors for "undefined index", "undefined array key" and "trying to read property of null".
     *
     * @void
     */
    public function mute_undefined_or_null_warnings(): void
    {
        $this->is_muting_undefined_or_null_warnings = true;
    }
    /**
     * Indicates if Smarty will mute errors for "undefined index", "undefined array key" and "trying to read property of null".
     */
    public function is_muting_undefined_or_null_warnings(): bool
    {
        return $this->is_muting_undefined_or_null_warnings;
    }
    /**
     * Empty cache for a specific template
     *
     * @param string $template_name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @param integer $exp_time expiration time
     * @param string $type resource type
     *
     * @return int number of cache files deleted
     * @throws \Smarty\Exception
     *
     * @api  Smarty::clearCache()
     */
    public function clear_cache($template_name, $cache_id = null, $compile_id = null, $exp_time = null)
    {
        return $this->get_cache_resource()->clear($this, $template_name, $cache_id, $compile_id, $exp_time);
    }
    /**
     * Empty cache folder
     *
     * @param integer $exp_time expiration time
     * @param string $type resource type
     *
     * @return int number of cache files deleted
     *
     * @api  Smarty::clearAllCache()
     */
    public function clear_all_cache($exp_time = null)
    {
        return $this->get_cache_resource()->clear_all($this, $exp_time);
    }
    /**
     * Delete compiled template file
     *
     * @param string $resource_name template name
     * @param string $compile_id compile id
     * @param integer $exp_time expiration time
     *
     * @return int number of template files deleted
     * @throws \Smarty\Exception
     *
     * @api  Smarty::clearCompiledTemplate()
     */
    public function clear_compiled_template($resource_name = null, $compile_id = null, $exp_time = null): int
    {
        $_compile_dir = $this->get_compile_dir();
        if ($_compile_dir === '/') {
            //We should never want to delete this!
            return 0;
        }
        $_compile_id = isset($compile_id) ? preg_replace('![^\w]+!', '_', $compile_id) : null;
        $_dir_sep = $this->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
        if (isset($resource_name)) {
            $_save_stat = $this->caching;
            $this->caching = \Smarty\Smarty::CACHING_OFF;
            /* @var Template $tpl */
            $tpl = $this->do_create_template($resource_name);
            $this->caching = $_save_stat;
            if (!$tpl->get_source()->handler->recompiled && $tpl->get_source()->exists) {
                $_resource_part_1 = basename(str_replace('^', DIRECTORY_SEPARATOR, $tpl->get_compiled()->filepath));
                $_resource_part_1_length = strlen($_resource_part_1);
            } else {
                return 0;
            }
            $_resource_part_2 = str_replace('.php', '.cache.php', $_resource_part_1);
            $_resource_part_2_length = strlen($_resource_part_2);
        }
        $_dir = $_compile_dir;
        if ($this->use_sub_dirs && isset($_compile_id)) {
            $_dir .= $_compile_id . $_dir_sep;
        }
        if (isset($_compile_id)) {
            $_compile_id_part = $_compile_dir . $_compile_id . $_dir_sep;
            $_compile_id_part_length = strlen($_compile_id_part);
        }
        $_count = 0;
        try {
            $_compile_dirs = new Recursive_Directory_Iterator($_dir);
        } catch (\UnexpectedValueException $e) {
            // path not found / not a dir
            return 0;
        }
        $_compile = new Recursive_Iterator_Iterator($_compile_dirs, Recursive_Iterator_Iterator::CHILD_FIRST);
        foreach ($_compile as $_file) {
            if (substr(basename($_file->get_pathname()), 0, 1) === '.') {
                continue;
            }
            $_filepath = (string) $_file;
            if ($_file->is_dir()) {
                // delete folder if empty
                @rmdir($_file->get_pathname());
            } else {
                // delete only php files
                if (substr($_filepath, -4) !== '.php') {
                    continue;
                }
                $unlink = false;
                if ((!isset($_compile_id) || isset($_filepath[$_compile_id_part_length]) && $a = !strncmp($_filepath, $_compile_id_part, $_compile_id_part_length)) && (!isset($resource_name) || isset($_filepath[$_resource_part_1_length]) && substr_compare($_filepath, $_resource_part_1, -$_resource_part_1_length, $_resource_part_1_length) === 0 || isset($_filepath[$_resource_part_2_length]) && substr_compare($_filepath, $_resource_part_2, -$_resource_part_2_length, $_resource_part_2_length) === 0)) {
                    if (isset($exp_time)) {
                        if (is_file($_filepath) && time() - filemtime($_filepath) >= $exp_time) {
                            $unlink = true;
                        }
                    } else {
                        $unlink = true;
                    }
                }
                if ($unlink && is_file($_filepath) && @unlink($_filepath)) {
                    $_count++;
                    if (function_exists('opcache_invalidate') && (!function_exists('ini_get') || strlen(ini_get('opcache.restrict_api')) < 1)) {
                        opcache_invalidate($_filepath, true);
                    }
                }
            }
        }
        return $_count;
    }
    /**
     * Compile all template files
     *
     * @param string $extension file extension
     * @param bool $force_compile force all to recompile
     * @param int $time_limit
     * @param int $max_errors
     *
     * @return integer number of template files recompiled
     * @api Smarty::compileAllTemplates()
     *
     */
    public function compile_all_templates($extension = '.tpl', $force_compile = false, $time_limit = 0, $max_errors = null)
    {
        return $this->compile_all($extension, $force_compile, $time_limit, $max_errors);
    }
    /**
     * Compile all config files
     *
     * @param string $extension file extension
     * @param bool $force_compile force all to recompile
     * @param int $time_limit
     * @param int $max_errors
     *
     * @return int number of template files recompiled
     * @api Smarty::compileAllConfig()
     *
     */
    public function compile_all_config($extension = '.conf', $force_compile = false, $time_limit = 0, $max_errors = null)
    {
        return $this->compile_all($extension, $force_compile, $time_limit, $max_errors, true);
    }
    /**
     * Compile all template or config files
     *
     * @param string $extension template file name extension
     * @param bool $force_compile force all to recompile
     * @param int $time_limit set maximum execution time
     * @param int $max_errors set maximum allowed errors
     * @param bool $isConfig flag true if called for config files
     *
     * @return int number of template files compiled
     */
    protected function compile_all($extension, $force_compile, $time_limit, $max_errors, $is_config = false): int
    {
        // switch off time limit
        if (function_exists('set_time_limit')) {
            @set_time_limit($time_limit);
        }
        $_count = 0;
        $_error_count = 0;
        $source_dir = $is_config ? $this->get_config_dir() : $this->get_template_dir();
        // loop over array of source directories
        foreach ($source_dir as $_dir) {
            $_dir_1 = new Recursive_Directory_Iterator($_dir, defined('FilesystemIterator::FOLLOW_SYMLINKS') ? Filesystem_Iterator::FOLLOW_SYMLINKS : 0);
            $_dir_2 = new Recursive_Iterator_Iterator($_dir_1);
            foreach ($_dir_2 as $_fileinfo) {
                $_file = $_fileinfo->get_filename();
                if (substr(basename($_fileinfo->get_pathname()), 0, 1) === '.') {
                    continue;
                }
                if (strpos($_file, '.svn') !== false) {
                    continue;
                }
                if (substr_compare($_file, $extension, -strlen($extension)) !== 0) {
                    continue;
                }
                if ($_fileinfo->get_path() !== substr($_dir, 0, -1)) {
                    $_file = substr($_fileinfo->get_path(), strlen($_dir)) . DIRECTORY_SEPARATOR . $_file;
                }
                echo "\n", $_dir, '---', $_file;
                flush();
                $_start_time = microtime(true);
                $_smarty = clone $this;
                $_smarty->force_compile = $force_compile;
                try {
                    $_tpl = $this->do_create_template($_file);
                    $_tpl->caching = self::CACHING_OFF;
                    $_tpl->set_source($is_config ? \Smarty\Template\Config::load($_tpl) : \Smarty\Template\Source::load($_tpl));
                    if ($_tpl->must_compile()) {
                        $_tpl->compile_template_source();
                        $_count++;
                        echo ' compiled in  ', microtime(true) - $_start_time, ' seconds';
                        flush();
                    } else {
                        echo ' is up to date';
                        flush();
                    }
                } catch (\Exception $e) {
                    echo "\n        ------>Error: ", $e->get_message(), "\n";
                    $_error_count++;
                }
                // free memory
                unset($_tpl);
                if ($max_errors !== null && $_error_count === $max_errors) {
                    echo "\ntoo many errors\n";
                    exit(1);
                }
            }
        }
        echo "\n";
        return $_count;
    }
    /**
     * check client side cache
     *
     * @param string $content
     * @throws \Exception
     * @throws \Smarty\Exception
     */
    public function cache_modified_check(Template\Cached $cached, Template $_template, $content): void
    {
        $_is_cached = $_template->is_cached() && !$_template->get_compiled()->get_nocache_code();
        $_last_modified_date = '';
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $_last_modified_date = @substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'], 'GMT') + 3);
        }
        if ($_is_cached && $cached->timestamp <= strtotime($_last_modified_date)) {
            switch (PHP_SAPI) {
                case 'cgi':
                // php-cgi < 5.3
                case 'cgi-fcgi':
                // php-cgi >= 5.3
                case 'fpm-fcgi':
                    // php-fpm >= 5.3.3
                    header('Status: 304 Not Modified');
                    break;
                case 'cli':
                    if (!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS'])) {
                        $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = '304 Not Modified';
                    }
                    break;
                default:
                    if (!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS'])) {
                        $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = '304 Not Modified';
                    } else {
                        header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
                    }
                    break;
            }
        } else {
            switch (PHP_SAPI) {
                case 'cli':
                    if (!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS'])) {
                        $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $cached->timestamp) . ' GMT';
                    }
                    break;
                default:
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $cached->timestamp) . ' GMT');
                    break;
            }
            echo $content;
        }
    }
    public function get_modifier_callback(string $modifier_name): ?array
    {
        foreach ($this->get_extensions() as $extension) {
            if ($callback = $extension->get_modifier_callback($modifier_name)) {
                return [new Callback_Wrapper($modifier_name, $callback), 'handle'];
            }
        }
        return null;
    }
    public function get_function_handler(string $function_name): ?\Smarty\Function_Handler\Function_Handler_Interface
    {
        foreach ($this->get_extensions() as $extension) {
            if ($handler = $extension->get_function_handler($function_name)) {
                return $handler;
            }
        }
        return null;
    }
    public function get_block_handler(string $block_tag_name): ?\Smarty\Block_Handler\Block_Handler_Interface
    {
        foreach ($this->get_extensions() as $extension) {
            if ($handler = $extension->get_block_handler($block_tag_name)) {
                return $handler;
            }
        }
        return null;
    }
    public function get_modifier_compiler(string $modifier): ?\Smarty\Compile\Modifier\Modifier_Compiler_Interface
    {
        foreach ($this->get_extensions() as $extension) {
            if ($handler = $extension->get_modifier_compiler($modifier)) {
                return $handler;
            }
        }
        return null;
    }
    /**
     * Run pre-filters over template source
     *
     * @param string $source the content which shall be processed by the filters
     * @param Template $template template object
     *
     * @return string                   the filtered source
     */
    public function run_pre_filters($source, Template $template)
    {
        foreach ($this->get_extensions() as $extension) {
            /** @var \Smarty\Filter\FilterInterface $filter */
            foreach ($extension->get_pre_filters() as $filter) {
                $source = $filter->filter($source, $template);
            }
        }
        // return filtered output
        return $source;
    }
    /**
     * Run post-filters over template's compiled code
     *
     * @param string $code the content which shall be processed by the filters
     * @param Template $template template object
     *
     * @return string                   the filtered code
     */
    public function run_post_filters($code, Template $template)
    {
        foreach ($this->get_extensions() as $extension) {
            /** @var \Smarty\Filter\FilterInterface $filter */
            foreach ($extension->get_post_filters() as $filter) {
                $code = $filter->filter($code, $template);
            }
        }
        // return filtered output
        return $code;
    }
    /**
     * Run filters over template output
     *
     * @param string $content the content which shall be processed by the filters
     * @param Template $template template object
     *
     * @return string                   the filtered (modified) output
     */
    public function run_output_filters($content, Template $template)
    {
        foreach ($this->get_extensions() as $extension) {
            /** @var \Smarty\Filter\FilterInterface $filter */
            foreach ($extension->get_output_filters() as $filter) {
                $content = $filter->filter($content, $template);
            }
        }
        // return filtered output
        return $content;
    }
    /**
     * Writes file in a safe way to disk
     *
     * @param string $_filepath complete filepath
     * @param string $_contents file content
     *
     * @return boolean true
     * @throws Exception
     */
    public function write_file($_filepath, $_contents): bool
    {
        $_error_reporting = error_reporting();
        error_reporting($_error_reporting & ~E_NOTICE & ~E_WARNING);
        $_dirpath = dirname($_filepath);
        // if subdirs, create dir structure
        if ($_dirpath !== '.') {
            $i = 0;
            // loop if concurrency problem occurs
            // see https://bugs.php.net/bug.php?id=35326
            while (!is_dir($_dirpath)) {
                if (@mkdir($_dirpath, 0777, true)) {
                    break;
                }
                clearstatcache();
                if (++$i === 3) {
                    error_reporting($_error_reporting);
                    throw new Exception("unable to create directory {$_dirpath}");
                }
                sleep(1);
            }
        }
        // write to tmp file, then move to overt file lock race condition
        $_tmp_file = $_dirpath . DIRECTORY_SEPARATOR . str_replace(['.', ','], '_', uniqid('wrt', true));
        if (!file_put_contents($_tmp_file, $_contents)) {
            error_reporting($_error_reporting);
            throw new Exception("unable to write file {$_tmp_file}");
        }
        /*
         * Windows' rename() fails if the destination exists,
         * Linux' rename() properly handles the overwrite.
         * Simply unlink()ing a file might cause other processes
         * currently reading that file to fail, but linux' rename()
         * seems to be smart enough to handle that for us.
         */
        if (\Smarty\Smarty::$_IS_WINDOWS) {
            // remove original file
            if (is_file($_filepath)) {
                @unlink($_filepath);
            }
            // rename tmp file
            $success = @rename($_tmp_file, $_filepath);
        } else {
            // rename tmp file
            $success = @rename($_tmp_file, $_filepath);
            if (!$success) {
                // remove original file
                if (is_file($_filepath)) {
                    @unlink($_filepath);
                }
                // rename tmp file
                $success = @rename($_tmp_file, $_filepath);
            }
        }
        if (!$success) {
            error_reporting($_error_reporting);
            throw new Exception("unable to write file {$_filepath}");
        }
        // set file permissions
        @chmod($_filepath, 0666 & ~umask());
        error_reporting($_error_reporting);
        return true;
    }
    private $runtimes = [];
    /**
     * Loads and returns a runtime extension or null if not found
     *
     *
     * @return object|null
     */
    public function get_runtime(string $type)
    {
        if (isset($this->runtimes[$type])) {
            return $this->runtimes[$type];
        }
        // Lazy load runtimes when/if needed
        switch ($type) {
            case 'Capture':
                return $this->runtimes[$type] = new Capture_Runtime();
            case 'Foreach':
                return $this->runtimes[$type] = new Foreach_Runtime();
            case 'Inheritance':
                return $this->runtimes[$type] = new Inheritance_Runtime();
            case 'TplFunction':
                return $this->runtimes[$type] = new Tpl_Function_Runtime();
            case 'DefaultPluginHandler':
                return $this->runtimes[$type] = new Default_Plugin_Handler_Runtime($this->get_default_plugin_handler_func());
        }
        throw new \Smarty\Exception('Trying to load invalid runtime ' . $type);
    }
    /**
     * Indicates if a runtime is available.
     *
     *
     */
    public function has_runtime(string $type): bool
    {
        try {
            $this->get_runtime($type);
            return true;
        } catch (\Smarty\Exception $e) {
            return false;
        }
    }
    public function get_default_plugin_handler_func(): ?callable
    {
        return $this->default_plugin_handler_func;
    }
    /**
     * load a filter of specified type and name
     *
     * @param string $type filter type
     * @param string $name filter name
     *
     * @throws \Smarty\Exception
     * @api  Smarty::loadFilter()
     * @deprecated since 5.0
     */
    public function load_filter($type, $name): bool
    {
        if ($type == \Smarty\Smarty::FILTER_VARIABLE) {
            foreach ($this->get_extensions() as $extension) {
                if ($extension->get_modifier_callback($name)) {
                    trigger_error('Using Smarty::loadFilter() to load variable filters is deprecated and will ' . 'be removed in a future release. Use Smarty::addDefaultModifiers() to add a modifier.', E_USER_DEPRECATED);
                    $this->add_default_modifiers([$name]);
                    return true;
                }
            }
        }
        trigger_error('Using Smarty::loadFilter() to load filters is deprecated and will be ' . 'removed in a future release. Use Smarty::addExtension() to add an extension or Smarty::registerFilter to ' . 'quickly register a filter using a callback function.', E_USER_DEPRECATED);
        if ($type == \Smarty\Smarty::FILTER_OUTPUT && $name == 'trimwhitespace') {
            $this->bc_plugins_adapter->add_output_filter(new Trim_Whitespace());
            return true;
        }
        $_plugin = "smarty_{$type}filter_{$name}";
        if (!is_callable($_plugin) && class_exists($_plugin, false)) {
            $_plugin = [$_plugin, 'execute'];
        }
        if (is_callable($_plugin)) {
            $this->register_filter($type, $_plugin, $name);
            return true;
        }
        throw new Exception("{$type}filter '{$name}' not found or callable");
    }
    /**
     * load a filter of specified type and name
     *
     * @param string $type filter type
     * @param string $name filter name
     *
     * @return static
     * @throws \Smarty\Exception
     * @api  Smarty::unloadFilter()
     *
     *
     * @deprecated since 5.0
     */
    public function unload_filter($type, $name)
    {
        trigger_error('Using Smarty::unloadFilter() to unload filters is deprecated and will be ' . 'removed in a future release. Use Smarty::addExtension() to add an extension or Smarty::(un)registerFilter to ' . 'quickly (un)register a filter using a callback function.', E_USER_DEPRECATED);
        return $this->unregister_filter($type, $name);
    }
    private $_caching_type = 'file';
    /**
     * @param $type
     *
     * @deprecated since 5.0
     */
    public function set_caching_type($type): void
    {
        trigger_error('Using Smarty::setCachingType() is deprecated and will be ' . 'removed in a future release. Use Smarty::setCacheResource() instead.', E_USER_DEPRECATED);
        $this->_caching_type = $type;
        $this->activate_bc_cache_resource();
    }
    /**
     * @deprecated since 5.0
     */
    public function get_caching_type(): string
    {
        trigger_error('Using Smarty::getCachingType() is deprecated and will be ' . 'removed in a future release.', E_USER_DEPRECATED);
        return $this->_caching_type;
    }
    /**
     * Registers a resource to fetch a template
     *
     * @param string $name name of resource type
     * @param Base $resource_handler
     *
     * @return static
     *
     * @api  Smarty::registerCacheResource()
     *
     * @deprecated since 5.0
     */
    public function register_cache_resource($name, \Smarty\Cacheresource\Base $resource_handler): self
    {
        trigger_error('Using Smarty::registerCacheResource() is deprecated and will be ' . 'removed in a future release. Use Smarty::setCacheResource() instead.', E_USER_DEPRECATED);
        $this->registered_cache_resources[$name] = $resource_handler;
        $this->activate_bc_cache_resource();
        return $this;
    }
    /**
     * Unregisters a resource to fetch a template
     *
     * @param                                                                 $name
     *
     * @return static
     * @api  Smarty::unregisterCacheResource()
     *
     * @deprecated since 5.0
     *
     */
    public function unregister_cache_resource($name): self
    {
        trigger_error('Using Smarty::unregisterCacheResource() is deprecated and will be ' . 'removed in a future release.', E_USER_DEPRECATED);
        if (isset($this->registered_cache_resources[$name])) {
            unset($this->registered_cache_resources[$name]);
        }
        return $this;
    }
    private function activate_bc_cache_resource(): void
    {
        if ($this->_caching_type == 'file') {
            $this->set_cache_resource(new File());
        }
        if (isset($this->registered_cache_resources[$this->_caching_type])) {
            $this->set_cache_resource($this->registered_cache_resources[$this->_caching_type]);
        }
    }
    /**
     * Registers a filter function
     *
     * @param string $type filter type
     * @param callable $callback
     * @param string|null $name optional filter name
     *
     * @return static
     * @throws \Smarty\Exception
     *
     * @api  Smarty::registerFilter()
     */
    public function register_filter($type, $callback, $name = null): self
    {
        $name = $name ?? $this->_get_filter_name($callback);
        if (!is_callable($callback)) {
            throw new Exception("{$type}filter '{$name}' not callable");
        }
        switch ($type) {
            case 'variable':
                $this->register_plugin(self::PLUGIN_MODIFIER, $name, $callback);
                trigger_error('Using Smarty::registerFilter() to register variable filters is deprecated and ' . 'will be removed in a future release. Use Smarty::addDefaultModifiers() to add a modifier.', E_USER_DEPRECATED);
                $this->add_default_modifiers([$name]);
                break;
            case 'output':
                $this->bc_plugins_adapter->add_callable_as_output_filter($callback, $name);
                break;
            case 'pre':
                $this->bc_plugins_adapter->add_callable_as_pre_filter($callback, $name);
                break;
            case 'post':
                $this->bc_plugins_adapter->add_callable_as_post_filter($callback, $name);
                break;
            default:
                throw new Exception("Illegal filter type '{$type}'");
        }
        return $this;
    }
    /**
     * Return internal filter name
     *
     * @param callback $callable
     *
     * @return string|null   internal filter name or null if callable cannot be serialized
     */
    private function _get_filter_name($callable): ?string
    {
        if (is_array($callable)) {
            $_class_name = is_object($callable[0]) ? get_class($callable[0]) : $callable[0];
            return $_class_name . '_' . $callable[1];
        }
        if (is_string($callable)) {
            return $callable;
        }
        return null;
    }
    /**
     * Unregisters a filter function. Smarty cannot unregister closures/anonymous functions if
     * no name was given in ::registerFilter.
     *
     * @param string $type filter type
     * @param callback|string $name the name previously used in ::registerFilter
     *
     * @return static
     * @throws \Smarty\Exception
     * @api  Smarty::unregisterFilter()
     *
     *
     */
    public function unregister_filter($type, $name): self
    {
        if (!is_string($name)) {
            $name = $this->_get_filter_name($name);
        }
        if ($name) {
            switch ($type) {
                case 'output':
                    $this->bc_plugins_adapter->remove_output_filter($name);
                    break;
                case 'pre':
                    $this->bc_plugins_adapter->remove_pre_filter($name);
                    break;
                case 'post':
                    $this->bc_plugins_adapter->remove_post_filter($name);
                    break;
                default:
                    throw new Exception("Illegal filter type '{$type}'");
            }
        }
        return $this;
    }
    /**
     * Add default modifiers
     *
     * @param array|string $modifiers modifier or list of modifiers
     *                                                                                   to add
     *
     * @return static
     * @api Smarty::addDefaultModifiers()
     *
     */
    public function add_default_modifiers($modifiers): self
    {
        if (is_array($modifiers)) {
            $this->default_modifiers = array_merge($this->default_modifiers, $modifiers);
        } else {
            $this->default_modifiers[] = $modifiers;
        }
        return $this;
    }
    /**
     * Get default modifiers
     *
     * @return array list of default modifiers
     * @api Smarty::getDefaultModifiers()
     *
     */
    public function get_default_modifiers()
    {
        return $this->default_modifiers;
    }
    /**
     * Set default modifiers
     *
     * @param array|string $modifiers modifier or list of modifiers
     *                                                                                   to set
     *
     * @return static
     * @api Smarty::setDefaultModifiers()
     *
     */
    public function set_default_modifiers($modifiers): self
    {
        $this->default_modifiers = (array) $modifiers;
        return $this;
    }
    public function get_cache_resource(): Cacheresource\Base
    {
        return $this->cache_resource;
    }
    public function set_cache_resource(Cacheresource\Base $cache_resource): void
    {
        $this->cache_resource = $cache_resource;
    }
    /**
     * fetches a rendered Smarty template
     *
     * @param string $template the resource handle of the template file or template object
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     *
     * @return string rendered template output
     * @throws Exception
     * @throws Exception
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null)
    {
        return $this->return_or_create_template($template, $cache_id, $compile_id)->fetch();
    }
    /**
     * displays a Smarty template
     *
     * @param string $template the resource handle of the template file or template object
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     *
     * @throws \Exception
     * @throws \Smarty\Exception
     */
    public function display($template = null, $cache_id = null, $compile_id = null): void
    {
        $this->return_or_create_template($template, $cache_id, $compile_id)->display();
    }
    /**
     * @param $resource_name
     * @param $cache_id
     * @param $compile_id
     * @param $parent
     * @param $caching
     * @param $cache_lifetime
     *
     * @throws Exception
     */
    public function do_create_template($resource_name, $cache_id = null, $compile_id = null, $parent = null, $caching = null, $cache_lifetime = null, bool $is_config = false, array $data = []): Template
    {
        if (!$this->_template_dir_normalized) {
            $this->_normalize_template_config(false);
        }
        $_template_id = $this->generate_unique_template_id($resource_name, $cache_id, $compile_id, $caching);
        if (!isset($this->templates[$_template_id])) {
            $new_template = new Template($resource_name, $this, $parent ?: $this, $cache_id, $compile_id, $caching, $is_config);
            $new_template->template_id = $_template_id;
            // @TODO this could go in constructor ^?
            $this->templates[$_template_id] = $new_template;
        }
        $tpl = clone $this->templates[$_template_id];
        $tpl->set_parent($parent ?: $this);
        if ($cache_lifetime) {
            $tpl->set_cache_lifetime($cache_lifetime);
        }
        // fill data if present
        foreach ($data as $_key => $_val) {
            $tpl->assign($_key, $_val);
        }
        $tpl->tpl_functions = array_merge($parent->tpl_functions ?? [], $tpl->tpl_functions ?? []);
        if (!$this->debugging && $this->debugging_ctrl === 'URL') {
            $tpl->get_smarty()->get_debug()->debug_url($tpl->get_smarty());
        }
        return $tpl;
    }
    /**
     * test if cache is valid
     *
     * @param null|string|Template $template the resource handle of the template file or template
     *                                                          object
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     *
     * @return bool cache status
     * @throws \Exception
     * @throws \Smarty\Exception
     *
     * @api  Smarty::isCached()
     */
    public function is_cached($template = null, $cache_id = null, $compile_id = null): bool
    {
        return $this->return_or_create_template($template, $cache_id, $compile_id)->is_cached();
    }
    /**
     * @param $template
     * @param $cache_id
     * @param $compile_id
     * @param $parent
     *
     * @return Template
     * @throws Exception
     */
    private function return_or_create_template($template, $cache_id = null, $compile_id = null)
    {
        if (!$template instanceof Template) {
            $template = $this->create_template($template, $cache_id, $compile_id, $this);
            $template->caching = $this->caching;
        }
        return $template;
    }
    /**
     * Sets if Smarty should check If-Modified-Since headers to determine cache validity.
     * @param bool $cache_modified_check
     */
    public function set_cache_modified_check($cache_modified_check): void
    {
        $this->cache_modified_check = (bool) $cache_modified_check;
    }
}