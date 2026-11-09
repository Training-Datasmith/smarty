<?php

declare(strict_types=1);

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.db2.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a database
 * -------------------------------------------------------------
 */

use Smarty\Resource\Recompiled_Plugin;
use Smarty\Template;
use Smarty\Template\Source;

class Smarty_Resource_Db2 extends Recompiled_Plugin
{
    public function populate(Source $source, ?Template $_template = null): void
    {
        $source->uid = sha1($source->resource);
        $source->timestamp = 0;
        $source->exists = true;
    }

    public function get_content(Source $source)
    {
        return '{$x="hello world"}{$x}';
    }
}
