<?php

declare (strict_types=1);
namespace Smarty\Template;

use Smarty\Smarty;
/**
 * Smarty Config Resource Data Object
 * Metadata Container for Config Files
 *
 * @author     Uwe Tews
 */
class Config extends Source
{
    /**
     * Flag that source is a config file
     *
     * @var bool
     */
    public $is_config = true;
    /**
     * @var array
     */
    protected static $_incompatible_resources = ['extends' => true];
    public function create_compiler(): \Smarty\Compiler\Base_Compiler
    {
        return new \Smarty\Compiler\Configfile($this->smarty);
    }
    protected static function get_default_handler_func(Smarty $smarty)
    {
        return $smarty->default_config_handler_func;
    }
}