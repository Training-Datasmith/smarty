<?php

declare (strict_types=1);
namespace Smarty\Template;

use Smarty\Exception;
use Smarty\Resource\File_Plugin;
use Smarty\Template;
/**
 * Base class for generated PHP files, such as compiled and cached versions of templates and config files.
 *
 * @author     Rodney Rehm
 */
abstract class Generated_Php_File
{
    /**
     * Compiled Filepath
     *
     * @var string
     */
    public $filepath;
    /**
     * Compiled Timestamp
     *
     * @var int|bool
     */
    public $timestamp = false;
    /**
     * Compiled Existence
     *
     * @var boolean
     */
    public $exists = false;
    /**
     * Template Compile Id (\Smarty\Template::$compile_id)
     *
     * @var string
     */
    public $compile_id;
    /**
     * Compiled Content Loaded
     *
     * @var boolean
     */
    protected $processed = false;
    /**
     * unique function name for compiled template code
     *
     * @var string
     */
    public $unifunc = '';
    /**
     * flag if template does contain nocache code sections
     *
     * @var bool
     */
    private $has_nocache_code = false;
    /**
     * resource file dependency
     *
     * @var array
     */
    public $file_dependency = [];
    /**
     * Get compiled time stamp
     *
     * @return int
     */
    public function get_time_stamp()
    {
        if ($this->exists && !$this->timestamp) {
            $this->timestamp = filemtime($this->filepath);
        }
        return $this->timestamp;
    }
    public function get_nocache_code(): bool
    {
        return $this->has_nocache_code;
    }
    public function set_nocache_code(bool $has_nocache_code): void
    {
        $this->has_nocache_code = $has_nocache_code;
    }
    /**
     * get rendered template content by calling compiled or cached template code
     *
     * @param string $unifunc function with template code
     *
     * @throws \Exception
     */
    protected function get_rendered_template_code(\Smarty\Template $_template, $unifunc)
    {
        $level = ob_get_level();
        try {
            if (empty($unifunc) || !function_exists($unifunc)) {
                throw new \Smarty\Exception("Invalid compiled template for '{$this->filepath}'");
            }
            $unifunc($_template);
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }
    }
    /**
     * @param $file_dependency
     *
     * @throws Exception
     */
    protected function check_file_dependencies($file_dependency, Template $_template): bool
    {
        // check file dependencies at compiled code
        foreach ($file_dependency as $_file_to_check) {
            $handler = \Smarty\Resource\Base_Plugin::load($_template->get_smarty(), $_file_to_check[2]);
            if ($handler instanceof File_Plugin) {
                if ($_template->get_source()->get_resource_name() === $_file_to_check[0]) {
                    // do not recheck current template
                    continue;
                }
                $mtime = $handler->get_resource_name_timestamp($_file_to_check[0], $_template->get_smarty(), $_template->get_source()->is_config);
            } else if ($handler->check_timestamps()) {
                // @TODO this doesn't actually check any dependencies, but only the main source file
                // and that might to be irrelevant, as the comment "do not recheck current template" above suggests
                $source = Source::load($_template, $_template->get_smarty());
                $mtime = $source->get_time_stamp();
            } else {
                continue;
            }
            if ($mtime === false || $mtime > $_file_to_check[1]) {
                return false;
            }
        }
        return true;
    }
}