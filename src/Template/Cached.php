<?php

declare (strict_types=1);
namespace Smarty\Template;

use Smarty\Exception;
use Smarty\Template;
/**
 * Represents a cached version of a template or config file.
 * @author     Rodney Rehm
 */
class Cached extends Generated_Php_File
{
    /**
     * Cache Is Valid
     *
     * @var boolean
     */
    private $valid;
    public function set_valid(?bool $valid): void
    {
        $this->valid = $valid;
    }
    /**
     * CacheResource Handler
     *
     * @var \Smarty\Cacheresource\Base
     */
    public $handler;
    /**
     * Template Cache Id (\Smarty\Template::$cache_id)
     *
     * @var string
     */
    public $cache_id;
    /**
     * saved cache lifetime in seconds
     *
     * @var int
     */
    public $cache_lifetime = 0;
    /**
     * Id for cache locking
     *
     * @var string
     */
    public $lock_id;
    /**
     * flag that cache is locked by this instance
     *
     * @var bool
     */
    public $is_locked = false;
    /**
     * Source Object
     *
     * @var Source
     */
    public $source;
    /**
     * Nocache hash codes of processed compiled templates
     *
     * @var array
     */
    public $hashes = [];
    /**
     * Content buffer
     *
     * @var string
     */
    public $content;
    /**
     * create Cached Object container
     *
     * @param $compile_id
     * @param $cache_id
     */
    public function __construct(Source $source, \Smarty\Cacheresource\Base $handler, $compile_id, $cache_id)
    {
        $this->compile_id = $compile_id;
        $this->cache_id = $cache_id;
        $this->source = $source;
        $this->handler = $handler;
    }
    /**
     * Render cache template
     *
     * @param bool $no_output_filter
     * @throws \Exception
     */
    public function render(Template $_template, $no_output_filter = true): void
    {
        if (!$this->is_cached($_template)) {
            $this->update_cache($_template, $no_output_filter);
        } else if (!$this->processed) {
            $this->process($_template);
        }
        if ($_template->get_smarty()->debugging) {
            $_template->get_smarty()->get_debug()->start_cache($_template);
        }
        $this->get_rendered_template_code($_template, $this->unifunc);
        if ($_template->get_smarty()->debugging) {
            $_template->get_smarty()->get_debug()->end_cache($_template);
        }
    }
    /**
     * Check if cache is valid, lock cache if required
     *
     *
     * @return bool flag true if cache is valid
     * @throws Exception
     */
    public function is_cached(Template $_template)
    {
        if ($this->valid !== null) {
            return $this->valid;
        }
        while (true) {
            while (true) {
                if ($this->exists === false || $_template->get_smarty()->force_compile || $_template->get_smarty()->force_cache) {
                    $this->valid = false;
                } else {
                    $this->valid = true;
                }
                if ($this->valid && $_template->caching === \Smarty\Smarty::CACHING_LIFETIME_CURRENT && $_template->cache_lifetime >= 0 && time() > $this->timestamp + $_template->cache_lifetime) {
                    // lifetime expired
                    $this->valid = false;
                }
                if ($this->valid && $_template->compile_check === \Smarty\Smarty::COMPILECHECK_ON && $_template->get_source()->get_time_stamp() > $this->timestamp) {
                    $this->valid = false;
                }
                if ($this->valid || !$_template->get_smarty()->cache_locking) {
                    break;
                }
                if (!$this->handler->locked($_template->get_smarty(), $this)) {
                    $this->handler->acquire_lock($_template->get_smarty(), $this);
                    break 2;
                }
                $this->handler->populate($this, $_template);
            }
            if ($this->valid) {
                if (!$_template->get_smarty()->cache_locking || $this->handler->locked($_template->get_smarty(), $this) === null) {
                    // load cache file for the following checks
                    if ($_template->get_smarty()->debugging) {
                        $_template->get_smarty()->get_debug()->start_cache($_template);
                    }
                    if ($this->handler->process($_template, $this) === false) {
                        $this->valid = false;
                    } else {
                        $this->processed = true;
                    }
                    if ($_template->get_smarty()->debugging) {
                        $_template->get_smarty()->get_debug()->end_cache($_template);
                    }
                } else {
                    $this->is_locked = true;
                    continue;
                }
            } else {
                return $this->valid;
            }
            if ($this->valid && $_template->caching === \Smarty\Smarty::CACHING_LIFETIME_SAVED && $_template->get_cached()->cache_lifetime >= 0 && time() > $_template->get_cached()->timestamp + $_template->get_cached()->cache_lifetime) {
                $this->valid = false;
            }
            if ($_template->get_smarty()->cache_locking) {
                if (!$this->valid) {
                    $this->handler->acquire_lock($_template->get_smarty(), $this);
                } elseif ($this->is_locked) {
                    $this->handler->release_lock($_template->get_smarty(), $this);
                }
            }
            return $this->valid;
        }
        return $this->valid;
    }
    /**
     * Process cached template
     *
     * @param Template $_template template object
     */
    private function process(Template $_template): void
    {
        if ($this->handler->process($_template, $this) === false) {
            $this->valid = false;
        }
        $this->processed = $this->valid;
    }
    /**
     * Read cache content from handler
     *
     * @param Template $_template template object
     *
     * @return string|false content
     */
    public function read_cache(Template $_template)
    {
        if (!$_template->get_source()->handler->recompiled) {
            return $this->handler->retrieve_cached_content($_template);
        }
        return false;
    }
    /**
     * Write this cache object to handler
     *
     * @param string $content content to cache
     *
     * @return bool success
     */
    public function write_cache(Template $_template, $content): bool
    {
        if (!$_template->get_source()->handler->recompiled) {
            if ($this->handler->store_cached_content($_template, $content)) {
                $this->content = null;
                $this->timestamp = time();
                $this->exists = true;
                $this->valid = true;
                $this->cache_lifetime = $_template->cache_lifetime;
                $this->processed = false;
                if ($_template->get_smarty()->cache_locking) {
                    $this->handler->release_lock($_template->get_smarty(), $this);
                }
                return true;
            }
            $this->content = null;
            $this->timestamp = false;
            $this->exists = false;
            $this->valid = false;
            $this->processed = false;
        }
        return false;
    }
    /**
     * Cache was invalid , so render from compiled and write to cache
     *
     * @param bool $no_output_filter
     * @throws \Smarty\Exception
     */
    private function update_cache(Template $_template, $no_output_filter): void
    {
        ob_start();
        $_template->get_compiled()->render($_template);
        if ($_template->get_smarty()->debugging) {
            $_template->get_smarty()->get_debug()->start_cache($_template);
        }
        $this->remove_no_cache_hash($_template, $no_output_filter);
        $this->process($_template);
        if ($_template->get_smarty()->debugging) {
            $_template->get_smarty()->get_debug()->end_cache($_template);
        }
    }
    /**
     * Sanitize content and write it to cache resource
     *
     * @param bool $no_output_filter
     * @throws \Smarty\Exception
     */
    private function remove_no_cache_hash(Template $_template, $no_output_filter): void
    {
        $php_pattern = '/(<%|%>|<\?php|<\?|\?>|<script\s+language\s*=\s*[\"\']?\s*php\s*[\"\']?\s*>)/';
        $content = ob_get_clean();
        $hash_array = $this->hashes;
        $hash_array[$_template->get_compiled()->nocache_hash] = true;
        $hash_array = array_keys($hash_array);
        $nocache_hash = '(' . implode('|', $hash_array) . ')';
        $_template->get_cached()->set_nocache_code(false);
        // get text between non-cached items
        $cache_split = preg_split("!/\\*%%SmartyNocache:{$nocache_hash}%%\\*\\/(.+?)/\\*/%%SmartyNocache:{$nocache_hash}%%\\*/!s", $content);
        // get non-cached items
        preg_match_all("!/\\*%%SmartyNocache:{$nocache_hash}%%\\*\\/(.+?)/\\*/%%SmartyNocache:{$nocache_hash}%%\\*/!s", $content, $cache_parts);
        $content = '';
        // loop over items, stitch back together
        foreach ($cache_split as $curr_idx => $curr_split) {
            if (preg_match($php_pattern, $curr_split)) {
                // escape PHP tags in template content
                $php_split = preg_split($php_pattern, $curr_split);
                preg_match_all($php_pattern, $curr_split, $php_parts);
                foreach ($php_split as $idx_php => $curr_php) {
                    $content .= $curr_php;
                    if (isset($php_parts[0][$idx_php])) {
                        $content .= "<?php echo '{$php_parts[1][$idx_php]}'; ?>\n";
                    }
                }
            } else {
                $content .= $curr_split;
            }
            if (isset($cache_parts[0][$curr_idx])) {
                $_template->get_cached()->set_nocache_code(true);
                $content .= $cache_parts[2][$curr_idx];
            }
        }
        if (!$no_output_filter && !$_template->get_cached()->get_nocache_code()) {
            $content = $_template->get_smarty()->run_output_filters($content, $_template);
        }
        $codeframe = (new \Smarty\Compiler\Code_Frame($_template))->create($content, '', true);
        $this->write_cache($_template, $codeframe);
    }
    public function get_source(): ?Source
    {
        return $this->source;
    }
    public function set_source(?Source $source): void
    {
        $this->source = $source;
    }
    /**
     * Returns the generated content
     *
     *
     * @return string|null
     * @throws \Exception
     */
    public function get_content(Template $template)
    {
        ob_start();
        $this->render($template);
        return ob_get_clean();
    }
    /**
     * This function is executed automatically when a generated file is included
     * - Decode saved properties
     * - Check if file is valid
     *
     * @param array $properties special template properties
     *
     * @return bool flag if compiled or cache file is valid
     * @throws Exception
     */
    public function is_fresh(Template $_template, array $properties): bool
    {
        // on cache resources other than file check version stored in cache code
        if (\Smarty\Smarty::SMARTY_VERSION !== $properties['version']) {
            return false;
        }
        $is_valid = true;
        if (!empty($properties['file_dependency']) && $_template->compile_check === \Smarty\Smarty::COMPILECHECK_ON) {
            $is_valid = $this->check_file_dependencies($properties['file_dependency'], $_template);
        }
        // CACHING_LIFETIME_SAVED cache expiry has to be validated here since otherwise we'd define the unifunc
        if ($_template->caching === \Smarty\Smarty::CACHING_LIFETIME_SAVED && $properties['cache_lifetime'] >= 0 && time() > $this->timestamp + $properties['cache_lifetime']) {
            $is_valid = false;
        }
        $this->cache_lifetime = $properties['cache_lifetime'];
        $this->set_valid($is_valid);
        if ($is_valid) {
            $this->unifunc = $properties['unifunc'];
            $this->set_nocache_code($properties['has_nocache_code']);
            $this->file_dependency = $properties['file_dependency'];
        }
        return $is_valid && !function_exists($properties['unifunc']);
    }
}