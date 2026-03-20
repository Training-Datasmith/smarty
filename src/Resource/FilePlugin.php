<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Resource File
 *
 * @author     Uwe Tews
 * @author     Rodney Rehm
 */
namespace Smarty\Resource;

use Smarty\Exception;
use Smarty\Smarty;
use Smarty\Template;
use Smarty\Template\Source;
/**
 * Smarty Internal Plugin Resource File
 * Implements the file system as resource for Smarty templates
 *
 */
class File_Plugin extends Base_Plugin
{
    /**
     * populate Source Object with metadata from Resource
     *
     * @param Source $source source object
     * @param Template|null $_template template object
     *
     * @throws Exception
     */
    public function populate(Source $source, ?Template $_template = null): void
    {
        $source->uid = sha1($source->name . ($source->is_config ? $source->get_smarty()->_joined_config_dir : $source->get_smarty()->_joined_template_dir));
        if ($path = $this->get_file_path($source->name, $source->get_smarty(), $source->is_config)) {
            if (isset($source->get_smarty()->security_policy) && is_object($source->get_smarty()->security_policy)) {
                $source->get_smarty()->security_policy->is_trusted_resource_dir($path, $source->is_config);
            }
            $source->exists = true;
            $source->timestamp = filemtime($path);
        } else {
            $source->timestamp = $source->exists = false;
        }
    }
    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Source $source source object
     */
    public function populate_timestamp(Source $source): void
    {
        $path = $this->get_file_path($source->name, $source->get_smarty(), $source->is_config);
        if (!$source->exists) {
            $source->exists = $path !== false && is_file($path);
        }
        if ($source->exists && $path !== false) {
            $source->timestamp = filemtime($path);
        } else {
            $source->timestamp = 0;
        }
    }
    /**
     * Load template's source from file into current template object
     *
     * @param Source $source source object
     *
     * @return string                 template source
     * @throws Exception        if source cannot be loaded
     */
    public function get_content(Source $source)
    {
        if ($source->exists) {
            return file_get_contents($this->get_file_path($source->get_resource_name(), $source->get_smarty(), $source->is_config()));
        }
        throw new Exception('Unable to read ' . ($source->is_config ? 'config' : 'template') . " {$source->type} '{$source->name}'");
    }
    /**
     * Determine basename for compiled filename
     *
     * @param Source $source source object
     *
     * @return string                 resource's basename
     */
    public function get_basename(Source $source): string
    {
        return basename($source->get_resource_name());
    }
    /**
     * build template filepath by traversing the template_dir array
     *
     * @param $file
     *
     * @return string fully qualified filepath
     */
    public function get_file_path($file, \Smarty\Smarty $smarty, bool $is_config = false)
    {
        // absolute file ?
        if ($file[0] === '/' || isset($file[1]) && $file[1] === ':') {
            $file = $smarty->_realpath($file, true);
            return is_file($file) ? $file : false;
        }
        // normalize DIRECTORY_SEPARATOR
        if (strpos($file, DIRECTORY_SEPARATOR === '/' ? '\\' : '/') !== false) {
            $file = str_replace(DIRECTORY_SEPARATOR === '/' ? '\\' : '/', DIRECTORY_SEPARATOR, $file);
        }
        $_directories = $smarty->get_template_dir(null, $is_config);
        // template_dir index?
        if ($file[0] === '[' && preg_match('#^\[([^\]]+)\](.+)$#', $file, $file_match)) {
            $file = $file_match[2];
            $_indices = explode(',', $file_match[1]);
            $_index_dirs = [];
            foreach ($_indices as $index) {
                $index = trim($index);
                // try string indexes
                if (isset($_directories[$index])) {
                    $_index_dirs[] = $_directories[$index];
                } elseif (is_numeric($index)) {
                    // try numeric index
                    $index = (int) $index;
                    if (isset($_directories[$index])) {
                        $_index_dirs[] = $_directories[$index];
                    } else {
                        // try at location index
                        $keys = array_keys($_directories);
                        if (isset($_directories[$keys[$index]])) {
                            $_index_dirs[] = $_directories[$keys[$index]];
                        }
                    }
                }
            }
            if (empty($_index_dirs)) {
                // index not found
                return false;
            }
            $_directories = $_index_dirs;
        }
        // relative file name?
        foreach ($_directories as $_directory) {
            $path = $_directory . $file;
            if (is_file($path)) {
                return strpos($path, '.' . DIRECTORY_SEPARATOR) !== false ? $smarty->_realpath($path) : $path;
            }
        }
        if (!isset($_index_dirs)) {
            // Could be relative to cwd
            $path = $smarty->_realpath($file, true);
            if (is_file($path)) {
                return $path;
            }
        }
        return false;
    }
    /**
     * Returns the timestamp of the resource indicated by $resourceName, or false if it doesn't exist.
     *
     *
     * @return false|int
     */
    public function get_resource_name_timestamp(string $resource_name, \Smarty\Smarty $smarty, bool $is_config = false)
    {
        if ($path = $this->get_file_path($resource_name, $smarty, $is_config)) {
            return filemtime($path);
        }
        return false;
    }
}