<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Template
 * This file contains the Smarty template engine
 *
 * @author     Uwe Tews
 */
namespace Smarty;

use Smarty\Runtime\Inheritance_Runtime;
use Smarty\Template\Cached;
use Smarty\Template\Compiled;
use Smarty\Template\Config;
use Smarty\Template\Source;
/**
 * Main class with template data structures and methods
 */
#[\Allow_Dynamic_Properties]
class Template extends Template_Base
{
    /**
     * caching mode to create nocache code but no cache file
     */
    public const CACHING_NOCACHE_CODE = 9999;
    /**
     * @var Compiled
     */
    private $compiled;
    /**
     * @var Cached
     */
    private $cached;
    /**
     * @var \Smarty\Compiler\Template
     */
    private $compiler;
    /**
     * Source instance
     *
     * @var Source|Config
     */
    private $source;
    /**
     * Template resource
     *
     * @var string
     */
    public $template_resource;
    /**
     * Template ID
     *
     * @var null|string
     */
    public $template_id;
    /**
     * Callbacks called before rendering template
     *
     * @var callback[]
     */
    public $start_render_callbacks = [];
    /**
     * Callbacks called after rendering template
     *
     * @var callback[]
     */
    public $end_render_callbacks = [];
    /**
     * Template left-delimiter. If null, defaults to $this->getSmarty()-getLeftDelimiter().
     *
     * @var string
     */
    private $left_delimiter;
    /**
     * Template right-delimiter. If null, defaults to $this->getSmarty()-getRightDelimiter().
     *
     * @var string
     */
    private $right_delimiter;
    /**
     * @var InheritanceRuntime|null
     */
    private $inheritance;
    /**
     * Create template data object
     * Some of the global Smarty settings copied to template scope
     * It load the required template resources and caching plugins
     *
     * @param string $template_resource template resource string
     * @param Smarty $smarty Smarty instance
     * @param \Smarty\Data|null $_parent back pointer to parent object with variables or null
     * @param mixed $_cache_id cache   id or null
     * @param mixed $_compile_id compile id or null
     * @param bool|int|null $_caching use caching?
     * @param bool $_isConfig
     *
     * @throws \Smarty\Exception
     */
    public function __construct($template_resource, Smarty $smarty, ?\Smarty\Data $_parent = null, $_cache_id = null, $_compile_id = null, $_caching = null, $_is_config = false)
    {
        $this->smarty = $smarty;
        // Smarty parameter
        $this->cache_id = $_cache_id ?? $this->smarty->cache_id;
        $this->compile_id = $_compile_id ?? $this->smarty->compile_id;
        $this->caching = (int) ($_caching ?? $this->smarty->caching);
        $this->cache_lifetime = $this->smarty->cache_lifetime;
        $this->compile_check = (int) $smarty->compile_check;
        $this->parent = $_parent;
        // Template resource
        $this->template_resource = $template_resource;
        $this->source = $_is_config ? Config::load($this) : Source::load($this);
        $this->compiled = Compiled::load($this);
        if ($smarty->security_policy) {
            $smarty->security_policy->register_call_backs($this);
        }
    }
    /**
     * render template
     *
     * @param bool $no_output_filter if true do not run output filter
     * @param null|bool $display true: display, false: fetch null: sub-template
     *
     * @return string
     * @throws \Exception
     * @throws \Smarty\Exception
     */
    private function render(bool $no_output_filter = true, $display = null)
    {
        if ($this->smarty->debugging) {
            $this->smarty->get_debug()->start_template($this, $display);
        }
        // checks if template exists
        if ($this->compile_check && !$this->get_source()->exists) {
            throw new Exception("Unable to load '{$this->get_source()->type}:{$this->get_source()->name}'" . ($this->_is_sub_tpl() ? " in '{$this->parent->template_resource}'" : ''));
        }
        // disable caching for evaluated code
        if ($this->get_source()->handler->recompiled) {
            $this->caching = \Smarty\Smarty::CACHING_OFF;
        }
        foreach ($this->start_render_callbacks as $callback) {
            call_user_func($callback, $this);
        }
        try {
            // read from cache or render
            if ($this->caching === \Smarty\Smarty::CACHING_LIFETIME_CURRENT || $this->caching === \Smarty\Smarty::CACHING_LIFETIME_SAVED) {
                $this->get_cached()->render($this, $no_output_filter);
            } else {
                $this->get_compiled()->render($this);
            }
        } finally {
            foreach ($this->end_render_callbacks as $callback) {
                call_user_func($callback, $this);
            }
        }
        // display or fetch
        if ($display) {
            if ($this->caching && $this->smarty->cache_modified_check) {
                $this->smarty->cache_modified_check($this->get_cached(), $this, $content ?? ob_get_clean());
            } else if ((!$this->caching || $this->get_cached()->get_nocache_code() || $this->get_source()->handler->recompiled) && !$no_output_filter) {
                echo $this->smarty->run_output_filters(ob_get_clean(), $this);
            } else {
                echo ob_get_clean();
            }
            if ($this->smarty->debugging) {
                $this->smarty->get_debug()->end_template($this);
                // debug output
                $this->smarty->get_debug()->display_debug($this, true);
            }
            return '';
        }
        if ($this->smarty->debugging) {
            $this->smarty->get_debug()->end_template($this);
            if ($this->smarty->debugging === 2 && $display === false) {
                $this->smarty->get_debug()->display_debug($this, true);
            }
        }
        if (!$no_output_filter && (!$this->caching || $this->get_cached()->get_nocache_code() || $this->get_source()->handler->recompiled)) {
            return $this->smarty->run_output_filters(ob_get_clean(), $this);
        }
        // return cache content
        return null;
    }
    /**
     * Runtime function to render sub-template
     *
     * @param string $template_name template name
     * @param mixed $cache_id cache id
     * @param mixed $compile_id compile id
     * @param integer $caching cache mode
     * @param integer $cache_lifetime lifetime of cache data
     * @param array $extra_vars passed parameter template variables
     *
     * @throws Exception
     */
    public function render_sub_template($template_name, $cache_id, $compile_id, $caching, $cache_lifetime, array $extra_vars = [], ?int $scope = null, ?string $current_dir = null): void
    {
        $name = $this->parse_resource_name($template_name);
        if ($current_dir && preg_match('/^\.{1,2}\//', $name)) {
            // relative template resource name, append it to current template name
            $template_name = $current_dir . DIRECTORY_SEPARATOR . $name;
        }
        $tpl = $this->smarty->do_create_template($template_name, $cache_id, $compile_id, $this, $caching, $cache_lifetime);
        $tpl->inheritance = $this->get_inheritance();
        // re-use the same Inheritance object inside the inheritance tree
        if ($scope) {
            $tpl->default_scope = $scope;
        }
        if ($caching) {
            if ($tpl->template_id !== $this->template_id && $caching !== \Smarty\Template::CACHING_NOCACHE_CODE) {
                $tpl->get_cached(true);
            } else {
                // re-use the same Cache object across subtemplates to gather hashes and file dependencies.
                $tpl->set_cached($this->get_cached());
            }
        }
        foreach ($extra_vars as $_key => $_val) {
            $tpl->assign($_key, $_val);
        }
        if ($tpl->caching === \Smarty\Template::CACHING_NOCACHE_CODE) {
            if ($tpl->get_compiled()->get_nocache_code()) {
                $this->get_cached()->hashes[$tpl->get_compiled()->nocache_hash] = true;
            }
        }
        $tpl->render();
    }
    /**
     * Remove type indicator from resource name if present.
     * E.g. $this->parseResourceName('file:template.tpl') returns 'template.tpl'
     *
     * @note "C:/foo.tpl" was forced to file resource up till Smarty 3.1.3 (including).
     *
     * @param string $resource_name    template_resource or config_resource to parse
     */
    private function parse_resource_name($resource_name): string
    {
        if (preg_match('/^([A-Za-z0-9_\-]{2,}):/', $resource_name, $match)) {
            return substr($resource_name, strlen($match[0]));
        }
        return $resource_name;
    }
    /**
     * Check if this is a sub template
     *
     * @return bool true is sub template
     */
    public function _is_sub_tpl(): bool
    {
        return isset($this->parent) && $this->parent instanceof Template;
    }
    public function assign($tpl_var, $value = null, $nocache = false, $scope = null)
    {
        return parent::assign($tpl_var, $value, $nocache, $scope);
    }
    /**
     * Compiles the template
     * If the template is not evaluated the compiled template is saved on disk
     *
     * @TODO only used in compileAll and 1 unit test: can we move this and make compileAndWrite private?
     *
     * @throws \Exception
     */
    public function compile_template_source()
    {
        return $this->get_compiled()->compile_and_write($this);
    }
    /**
     * Return cached content
     *
     * @return null|string
     * @throws Exception
     */
    public function get_cached_content()
    {
        return $this->get_cached()->get_content($this);
    }
    /**
     * Writes the content to cache resource
     *
     * @param string $content
     *
     * @return bool
     *
     * @TODO this method is only used in unit tests that (mostly) try to test CacheResources.
     */
    public function write_cached_content($content)
    {
        if ($this->get_source()->handler->recompiled || !$this->caching) {
            // don't write cache file
            return false;
        }
        $codeframe = $this->create_code_frame($content, '', true);
        return $this->get_cached()->write_cache($this, $codeframe);
    }
    /**
     * Get unique template id
     *
     * @return string
     */
    public function get_template_id()
    {
        return $this->template_id;
    }
    /**
     * runtime error not matching capture tags
     *
     * @throws \Smarty\Exception
     */
    public function capture_error()
    {
        throw new Exception("Not matching {capture} open/close in '{$this->template_resource}'");
    }
    /**
     * Return Compiled object
     *
     * @param bool $forceNew force new compiled object
     */
    public function get_compiled($force_new = false)
    {
        if ($force_new || !isset($this->compiled)) {
            $this->compiled = Compiled::load($this);
        }
        return $this->compiled;
    }
    /**
     * Return Cached object
     *
     * @param bool $forceNew force new cached object
     *
     * @throws Exception
     */
    public function get_cached($force_new = false): Cached
    {
        if ($force_new || !isset($this->cached)) {
            $cache_resource = $this->smarty->get_cache_resource();
            $this->cached = new Cached($this->source, $cache_resource, $this->compile_id, $this->cache_id);
            if ($this->is_caching_enabled()) {
                $cache_resource->populate($this->cached, $this);
            } else {
                $this->cached->set_valid(false);
            }
        }
        return $this->cached;
    }
    private function is_caching_enabled(): bool
    {
        return $this->caching && !$this->get_source()->handler->recompiled;
    }
    /**
     * Helper function for InheritanceRuntime object
     *
     * @throws Exception
     */
    public function get_inheritance(): Inheritance_Runtime
    {
        if (is_null($this->inheritance)) {
            $this->inheritance = clone $this->get_smarty()->get_runtime('Inheritance');
        }
        return $this->inheritance;
    }
    /**
     * Sets a new InheritanceRuntime object.
     *
     *
     */
    public function set_inheritance(Inheritance_Runtime $inheritance_runtime): void
    {
        $this->inheritance = $inheritance_runtime;
    }
    /**
     * Return Compiler object
     */
    public function get_compiler()
    {
        if (!isset($this->compiler)) {
            $this->compiler = $this->get_source()->create_compiler();
        }
        return $this->compiler;
    }
    /**
     * Create code frame for compiled and cached templates
     *
     * @param string $content optional template content
     * @param string $functions compiled template function and block code
     * @param bool $cache flag for cache file
     *
     * @return string
     * @throws Exception
     */
    public function create_code_frame(string $content = '', string $functions = '', $cache = false, ?\Smarty\Compiler\Template $compiler = null)
    {
        return $this->get_code_frame_compiler()->create($content, $functions, $cache, $compiler);
    }
    /**
     * Template data object destructor
     */
    public function __destruct()
    {
        if ($this->smarty->cache_locking && $this->get_cached()->is_locked) {
            $this->get_cached()->handler->release_lock($this->smarty, $this->get_cached());
        }
    }
    /**
     * Returns if the current template must be compiled by the Smarty compiler
     * It does compare the timestamps of template source and the compiled templates and checks the force compile
     * configuration
     *
     * @throws \Smarty\Exception
     */
    public function must_compile(): bool
    {
        if (!$this->get_source()->exists) {
            if ($this->_is_sub_tpl()) {
                $parent_resource = " in '{$this->parent->template_resource}'";
            } else {
                $parent_resource = '';
            }
            throw new Exception("Unable to load {$this->get_source()->type} '{$this->get_source()->name}'{$parent_resource}");
        }
        // @TODO move this logic to Compiled
        return $this->smarty->force_compile || $this->get_source()->handler->recompiled || !$this->get_compiled()->exists || $this->compile_check && $this->get_compiled()->get_time_stamp() < $this->get_source()->get_time_stamp();
    }
    private function get_code_frame_compiler(): Compiler\Code_Frame
    {
        return new \Smarty\Compiler\Code_Frame($this);
    }
    /**
     * Get left delimiter
     *
     * @return string
     */
    public function get_left_delimiter()
    {
        return $this->left_delimiter ?? $this->get_smarty()->get_left_delimiter();
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
        return $this->right_delimiter ?? $this->get_smarty()->get_right_delimiter();
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
     * gets  a stream variable
     *
     * @param string                                                  $variable the stream of the variable
     *
     * @throws \Smarty\Exception
     *
     */
    public function get_stream_variable(string $variable): ?string
    {
        trigger_error("Using stream variables (\\`\\{\$foo:bar\\}\\`)is deprecated.", E_USER_DEPRECATED);
        $_result = '';
        $fp = fopen($variable, 'r');
        if ($fp) {
            while (!feof($fp) && ($current_line = fgets($fp)) !== false) {
                $_result .= $current_line;
            }
            fclose($fp);
            return $_result;
        }
        if ($this->get_smarty()->error_unassigned) {
            throw new Exception('Undefined stream variable "' . $variable . '"');
        }
        return null;
    }
    /**
     * @inheritdoc
     */
    public function config_load($config_file, $sections = null)
    {
        $conf_obj = parent::config_load($config_file, $sections);
        $this->get_compiled()->file_dependency[$conf_obj->get_source()->uid] = [$conf_obj->get_source()->get_resource_name(), $conf_obj->get_source()->get_time_stamp(), $conf_obj->get_source()->type];
        return $conf_obj;
    }
    public function fetch()
    {
        $result = $this->_execute(0);
        return $result ?? ob_get_clean();
    }
    public function display(): void
    {
        $this->_execute(1);
    }
    /**
     * test if cache is valid
     *
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     * @param object $parent next higher level of Smarty variables
     *
     * @return bool cache status
     * @throws \Exception
     * @throws \Smarty\Exception
     *
     * @api  Smarty::isCached()
     */
    public function is_cached(): bool
    {
        return (bool) $this->_execute(2);
    }
    /**
     * fetches a rendered Smarty template
     *
     * @param string $function function type 0 = fetch,  1 = display, 2 = isCache
     *
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     */
    private function _execute(int $function)
    {
        $smarty = $this->get_smarty();
        // make sure we have integer values
        $this->caching = (int) $this->caching;
        // fetch template content
        $level = ob_get_level();
        try {
            $_smarty_old_error_level = isset($smarty->error_reporting) ? error_reporting($smarty->error_reporting) : null;
            if ($smarty->is_muting_undefined_or_null_warnings()) {
                $error_handler = new \Smarty\Error_Handler();
                $error_handler->activate();
            }
            if ($function === 2) {
                if ($this->caching) {
                    // return cache status of template
                    $result = $this->get_cached()->is_cached($this);
                } else {
                    return false;
                }
            } else {
                // After rendering a template, the tpl/config variables are reset, so the template can be re-used.
                $this->push_stack();
                // Start output-buffering.
                ob_start();
                $result = $this->render(false, $function);
                // Restore the template to its previous state
                $this->pop_stack();
            }
            if (isset($error_handler)) {
                $error_handler->deactivate();
            }
            if (isset($_smarty_old_error_level)) {
                error_reporting($_smarty_old_error_level);
            }
            return $result;
        } catch (\Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            if (isset($error_handler)) {
                $error_handler->deactivate();
            }
            if (isset($_smarty_old_error_level)) {
                error_reporting($_smarty_old_error_level);
            }
            throw $e;
        }
    }
    /**
     * @return Config|Source|null
     */
    public function get_source()
    {
        return $this->source;
    }
    /**
     * @param Config|Source|null $source
     */
    public function set_source($source): void
    {
        $this->source = $source;
    }
    /**
     * Sets the Cached object, so subtemplates can share one Cached object to gather meta-data.
     *
     *
     */
    private function set_cached(Cached $cached): void
    {
        $this->cached = $cached;
    }
    /**
     * @param string $compile_id
     *
     * @throws Exception
     */
    public function set_compile_id($compile_id): void
    {
        parent::set_compile_id($compile_id);
        $this->get_compiled(true);
        if ($this->caching) {
            $this->get_cached(true);
        }
    }
    /**
     * @param string $cache_id
     *
     * @throws Exception
     */
    public function set_cache_id($cache_id): void
    {
        parent::set_cache_id($cache_id);
        $this->get_cached(true);
    }
}