<?php

declare (strict_types=1);
namespace Smarty\Cacheresource;

use Recursive_Directory_Iterator;
use Recursive_Iterator_Iterator;
use Smarty\Smarty;
use Smarty\Template;
use Smarty\Template\Cached;
/**
 * Smarty Internal Plugin CacheResource File
 *
 * @author     Uwe Tews
 * @author     Rodney Rehm
 */
/**
 * This class does contain all necessary methods for the HTML cache on file system
 * Implements the file system as resource for the HTML cache Version using nocache inserts.
 */
class File extends Base
{
    /**
     * populate Cached Object with metadata from Resource
     *
     * @param Cached   $cached    cached object
     * @param Template $_template template object
     */
    public function populate(Cached $cached, Template $_template): void
    {
        $source = $_template->get_source();
        $smarty = $_template->get_smarty();
        $_compile_dir_sep = $smarty->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
        $_filepath = $source->uid;
        $cached->filepath = $smarty->get_cache_dir();
        if (isset($_template->cache_id)) {
            $cached->filepath .= preg_replace(['![^\w|]+!', '![|]+!'], ['_', $_compile_dir_sep], $_template->cache_id) . $_compile_dir_sep;
        }
        if (isset($_template->compile_id)) {
            $cached->filepath .= preg_replace('![^\w]+!', '_', $_template->compile_id) . $_compile_dir_sep;
        }
        // if use_sub_dirs, break file into directories
        if ($smarty->use_sub_dirs) {
            $cached->filepath .= $_filepath[0] . $_filepath[1] . DIRECTORY_SEPARATOR . $_filepath[2] . $_filepath[3] . DIRECTORY_SEPARATOR . $_filepath[4] . $_filepath[5] . DIRECTORY_SEPARATOR;
        }
        $cached->filepath .= $_filepath . '_' . $source->get_basename();
        if ($smarty->cache_locking) {
            $cached->lock_id = $cached->filepath . '.lock';
        }
        $cached->filepath .= '.php';
        $cached->timestamp = $cached->exists = is_file($cached->filepath);
        if ($cached->exists) {
            $cached->timestamp = filemtime($cached->filepath);
        }
    }
    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @param Cached $cached cached object
     */
    public function populate_timestamp(Cached $cached): void
    {
        $cached->timestamp = $cached->exists = is_file($cached->filepath);
        if ($cached->exists) {
            $cached->timestamp = filemtime($cached->filepath);
        }
    }
    /**
     * Read the cached template and process its header
     *
     * @param Template $_smarty_tpl do not change variable name, is used by compiled template
     * @param Cached|null $cached cached object
     * @param bool $update flag if called because cache update
     *
     * @return boolean true or false if the cached content does not exist
     */
    public function process(Template $_smarty_tpl, ?Cached $cached = null, $update = false)
    {
        $_smarty_tpl->get_cached()->set_valid(false);
        if ($update && defined('HHVM_VERSION')) {
            eval('?>' . file_get_contents($_smarty_tpl->get_cached()->filepath));
            return true;
        }
        return @include $_smarty_tpl->get_cached()->filepath;
    }
    /**
     * Write the rendered template output to cache
     *
     * @param Template $_template template object
     * @param string                   $content   content to cache
     *
     * @return bool success
     * @throws \Smarty\Exception
     */
    public function store_cached_content(Template $_template, $content): bool
    {
        if ($_template->get_smarty()->write_file($_template->get_cached()->filepath, $content) === true) {
            if (function_exists('opcache_invalidate') && (!function_exists('ini_get') || strlen(ini_get('opcache.restrict_api')) < 1)) {
                opcache_invalidate($_template->get_cached()->filepath, true);
            }
            $cached = $_template->get_cached();
            $cached->timestamp = $cached->exists = is_file($cached->filepath);
            if ($cached->exists) {
                $cached->timestamp = filemtime($cached->filepath);
                return true;
            }
        }
        return false;
    }
    /**
     * Read cached template from cache
     *
     * @param Template $_template template object
     *
     * @return string  content
     */
    public function retrieve_cached_content(Template $_template)
    {
        if (is_file($_template->get_cached()->filepath)) {
            return file_get_contents($_template->get_cached()->filepath);
        }
        return false;
    }
    /**
     * Empty cache
     *
     * @param integer $exp_time expiration time (number of seconds, not timestamp)
     * @return integer number of cache files deleted
     */
    public function clear_all(Smarty $smarty, $exp_time = null)
    {
        return $this->clear($smarty, null, null, null, $exp_time);
    }
    /**
     * Empty cache for a specific template
     *
     * @param string  $resource_name template name
     * @param string  $cache_id      cache id
     * @param string  $compile_id    compile id
     * @param integer $exp_time      expiration time (number of seconds, not timestamp)
     * @return integer number of cache files deleted
     */
    public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time): int
    {
        $_cache_id = isset($cache_id) ? preg_replace('![^\w\|]+!', '_', $cache_id) : null;
        $_compile_id = isset($compile_id) ? preg_replace('![^\w]+!', '_', $compile_id) : null;
        $_dir_sep = $smarty->use_sub_dirs ? '/' : '^';
        $_compile_id_offset = $smarty->use_sub_dirs ? 3 : 0;
        $_dir = $smarty->get_cache_dir();
        if ($_dir === '/') {
            //We should never want to delete this!
            return 0;
        }
        $_dir_length = strlen($_dir);
        if (isset($_cache_id)) {
            $_cache_id_parts = explode('|', $_cache_id);
            $_cache_id_parts_count = count($_cache_id_parts);
            if ($smarty->use_sub_dirs) {
                foreach ($_cache_id_parts as $id_part) {
                    $_dir .= $id_part . '/';
                }
            }
        }
        if (isset($resource_name)) {
            $_save_stat = $smarty->caching;
            $smarty->caching = \Smarty\Smarty::CACHING_LIFETIME_CURRENT;
            $tpl = $smarty->do_create_template($resource_name);
            $smarty->caching = $_save_stat;
            // remove from template cache
            if ($tpl->get_source()->exists) {
                $_resourcename_parts = basename(str_replace('^', '/', $tpl->get_cached()->filepath));
            } else {
                return 0;
            }
        }
        $_count = 0;
        $_time = time();
        if (file_exists($_dir)) {
            $_cache_dirs = new Recursive_Directory_Iterator($_dir);
            $_cache = new Recursive_Iterator_Iterator($_cache_dirs, Recursive_Iterator_Iterator::CHILD_FIRST);
            foreach ($_cache as $_file) {
                if (substr(basename($_file->get_pathname()), 0, 1) === '.') {
                    continue;
                }
                $_filepath = (string) $_file;
                // directory ?
                if ($_file->is_dir()) {
                    // delete folder if empty
                    @rmdir($_file->get_pathname());
                } else {
                    // delete only php files
                    if (substr($_filepath, -4) !== '.php') {
                        continue;
                    }
                    $_parts = explode($_dir_sep, str_replace('\\', '/', substr($_filepath, $_dir_length)));
                    $_parts_count = count($_parts);
                    // check name
                    if (isset($resource_name)) {
                        if ($_parts[$_parts_count - 1] !== $_resourcename_parts) {
                            continue;
                        }
                    }
                    // check compile id
                    if (isset($_compile_id) && (!isset($_parts[$_parts_count - 2 - $_compile_id_offset]) || $_parts[$_parts_count - 2 - $_compile_id_offset] !== $_compile_id)) {
                        continue;
                    }
                    // check cache id
                    if (isset($_cache_id)) {
                        // count of cache id parts
                        $_parts_count = isset($_compile_id) ? $_parts_count - 2 - $_compile_id_offset : $_parts_count - 1 - $_compile_id_offset;
                        if ($_parts_count < $_cache_id_parts_count) {
                            continue;
                        }
                        for ($i = 0; $i < $_cache_id_parts_count; $i++) {
                            if ($_parts[$i] !== $_cache_id_parts[$i]) {
                                continue 2;
                            }
                        }
                    }
                    if (is_file($_filepath)) {
                        // expired ?
                        if (isset($exp_time)) {
                            if ($exp_time < 0) {
                                preg_match('#\'cache_lifetime\' =>\s*(\d*)#', file_get_contents($_filepath), $match);
                                if ($_time < filemtime($_filepath) + $match[1]) {
                                    continue;
                                }
                            } else if ($_time - filemtime($_filepath) < $exp_time) {
                                continue;
                            }
                        }
                        $_count += @unlink($_filepath) ? 1 : 0;
                        if (function_exists('opcache_invalidate') && (!function_exists('ini_get') || strlen(ini_get('opcache.restrict_api')) < 1)) {
                            opcache_invalidate($_filepath, true);
                        }
                    }
                }
            }
        }
        return $_count;
    }
    /**
     * Check is cache is locked for this template
     *
     * @param Smarty                 $smarty Smarty object
     * @param Cached $cached cached object
     *
     * @return boolean true or false if cache is locked
     */
    public function has_lock(Smarty $smarty, Cached $cached)
    {
        clearstatcache(true, $cached->lock_id ?? '');
        if (null !== $cached->lock_id && is_file($cached->lock_id)) {
            $t = filemtime($cached->lock_id);
            return $t && time() - $t < $smarty->locking_timeout;
        }
        return false;
    }
    /**
     * Lock cache for this template
     *
     * @param Smarty                 $smarty Smarty object
     * @param Cached $cached cached object
     */
    public function acquire_lock(Smarty $smarty, Cached $cached): void
    {
        $cached->is_locked = true;
        touch($cached->lock_id);
    }
    /**
     * Unlock cache for this template
     *
     * @param Smarty                 $smarty Smarty object
     * @param Cached $cached cached object
     */
    public function release_lock(Smarty $smarty, Cached $cached): void
    {
        $cached->is_locked = false;
        @unlink($cached->lock_id);
    }
}