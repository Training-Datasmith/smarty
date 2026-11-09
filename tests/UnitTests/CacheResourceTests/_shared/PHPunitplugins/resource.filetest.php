<?php

declare(strict_types=1);

use Smarty\Resource\File_Plugin;
use Smarty\Template;
use Smarty\Template\Source;

class Smarty_Resource_FiletestPlugin extends File_Plugin
{
    /**
     * populate Source Object with metadata from Resource
     *
     * @param Source   $source    source object
     * @param Template $_template template object
     */
    public function populate(Source $source, ?Template $_template = null): void
    {
        parent::populate($source, $_template);
        if ($source->exists) {
            if (isset(Cache_Resource_Test_Common::$touchResource[$source->get_resource_name()])) {
                $source->timestamp = Cache_Resource_Test_Common::$touchResource[$source->get_resource_name()];
            }
        }
    }

}
