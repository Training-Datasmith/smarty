<?php

declare(strict_types=1);

use Smarty\Exception;
use Smarty\Resource\File_Plugin;
use Smarty\Template;
use Smarty\Template\Source;

/**
 * Ambiguous Filename Custom Resource Example
 *

 * @author  Rodney Rehm
 */
class Smarty_Resource_AmbiguousPlugin extends File_Plugin
{
    protected $directory;
    protected $segment;

    public function __construct($directory)
    {
        $this->directory = rtrim($directory ?? '', '/\\') . DIRECTORY_SEPARATOR;
        //        parent::__construct();
    }

    public function setSegment($segment)
    {
        $this->segment = $segment;
    }

    /**
     * populate Source Object with meta data from Resource
     *
     * @param Source   $source    source object
     * @param Template $_template template object
     */
    public function populate(Source $source, ?Template $_template = null): void
    {
        $segment = '';
        if ($this->segment) {
            $segment = rtrim($this->segment, '/\\') . DIRECTORY_SEPARATOR;
        }

        $source->uid = sha1($segment . '#' . $source->get_resource_name());
        if ($_template->get_smarty()->get_compile_check() && !isset($source->timestamp)) {
            $source->timestamp = @filemtime($this->directory . $segment . $source->name);
            $source->exists = !!$source->timestamp;
        }
    }

    public function get_content(Source $source)
    {

        $segment = '';
        if ($this->segment) {
            $segment = rtrim($this->segment, '/\\') . DIRECTORY_SEPARATOR;
        }

        if ($source->exists) {
            return file_get_contents($this->directory . $segment . $source->name);
        }
        throw new Exception(
            'Unable to read ' . ($source->is_config ? 'config' : 'template') .
            " {$source->type} '{$source->name}'"
        );
    }
}
