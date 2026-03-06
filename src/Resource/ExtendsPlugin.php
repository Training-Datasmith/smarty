<?php

namespace Smarty\Resource;

use Smarty\Exception;
use Smarty\Template;
use Smarty\Template\Source;

/**
 * Smarty Internal Plugin Resource Extends
 * Implements the file system as resource for Smarty which {extend}s a chain of template files templates
 * @author     Uwe Tews
 * @author     Rodney Rehm
 */
class ExtendsPlugin extends BasePlugin
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
        $uid = '';
        $sources = [];
        $components = explode('|', $source->name);
        $smarty = $source->getSmarty();
        $exists = true;
        foreach ($components as $component) {
            $_s = Source::load(null, $smarty, $component);
            $sources[ $_s->uid ] = $_s;
            $uid .= $_s->uid;
            if ($_template) {
                $exists = $exists && $_s->exists;
            }
        }
        $source->components = $sources;
        $source->uid = sha1($uid . $source->getSmarty()->_joined_template_dir);
        $source->exists = $exists;
        if ($_template) {
            $source->timestamp = $_s->timestamp;
        }
    }

    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Source $source source object
     */
    public function populateTimestamp(Source $source): void
    {
        $source->exists = true;
        /* @var Source $_s */
        foreach ($source->components as $_s) {
            $source->exists = $source->exists && $_s->exists;
        }
        $source->timestamp = $source->exists ? $_s->getTimeStamp() : false;
    }

    /**
     * Load template's source from files into current template object
     *
     * @param Source $source source object
     *
     * @return string template source
     * @throws \Smarty\Exception if source cannot be loaded
     */
    public function getContent(Source $source): string
    {
        if (!$source->exists) {
            throw new \Smarty\Exception("Unable to load  '{$source->type}:{$source->name}'");
        }
        $_components = array_reverse($source->components);
        $_content = '';
        /* @var Source $_s */
        foreach ($_components as $_s) {
            // read content
            $_content .= $_s->getContent();
        }
        return $_content;
    }

    /**
     * Determine basename for compiled filename
     *
     * @param Source $source source object
     *
     * @return string resource's basename
     */
    public function getBasename(Source $source): string
    {
        $search = [':'];
        if (\Smarty\Smarty::$_IS_WINDOWS) {
            $search = [':', '|'];
        }
        return str_replace($search, '.', basename($source->getResourceName()));
    }

    /*
     * Disable timestamp checks for extends resource.
     * The individual source components will be checked.
     *
     * @return bool
     */
    public function checkTimestamps(): bool
    {
        return false;
    }
}
