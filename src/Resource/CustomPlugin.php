<?php

declare (strict_types=1);
/**
 * Smarty Resource Plugin
 *
 * @author     Rodney Rehm
 */
namespace Smarty\Resource;

use Smarty\Exception;
use Smarty\Smarty;
use Smarty\Template;
use Smarty\Template\Source;
/**
 * Smarty Resource Plugin
 * Wrapper Implementation for custom resource plugins
 *
 */
abstract class Custom_Plugin extends Base_Plugin
{
    /**
     * fetch template and its modification time from data source
     *
     * @param string $name template name
     * @param string  &$source template source
     * @param integer &$mtime template modification timestamp (epoch)
     */
    abstract protected function fetch($name, &$source, &$mtime);
    /**
     * Fetch template's modification timestamp from data source
     * {@internal implementing this method is optional.
     *  Only implement it if modification times can be accessed faster than loading the complete template source.}}
     *
     * @param string $name template name
     *
     * @return integer|boolean timestamp (epoch) the template was modified, or false if not found
     */
    protected function fetch_timestamp($name)
    {
        return null;
    }
    /**
     * populate Source Object with metadata from Resource
     *
     * @param Source $source source object
     * @param Template|null $_template template object
     */
    public function populate(Source $source, ?Template $_template = null): void
    {
        $source->uid = sha1($source->type . ':' . $source->name);
        $mtime = $this->fetch_timestamp($source->name);
        if ($mtime !== null) {
            $source->timestamp = $mtime;
        } else {
            $this->fetch($source->name, $content, $timestamp);
            $source->timestamp = $timestamp ?? false;
            $source->content = $content;
        }
        $source->exists = !!$source->timestamp;
    }
    /**
     * Load template's source into current template object
     *
     * @param Source $source source object
     *
     * @return string                 template source
     * @throws Exception        if source cannot be loaded
     */
    public function get_content(Source $source)
    {
        $this->fetch($source->name, $content, $timestamp);
        return $content;
    }
    /**
     * Determine basename for compiled filename
     *
     * @param Source $source source object
     *
     * @return string                 resource's basename
     */
    public function get_basename(Source $source)
    {
        return basename($this->generate_safe_name($source->name));
    }
    /**
     * Removes special characters from $name and limits its length to 127 characters.
     *
     * @param $name
     */
    private function generate_safe_name($name): string
    {
        return substr(preg_replace('/[^A-Za-z0-9._]/', '', (string) $name), 0, 127);
    }
}