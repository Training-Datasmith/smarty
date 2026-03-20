<?php

declare (strict_types=1);
/**
 * Smarty Resource Plugin
 *
 * @author     Rodney Rehm
 */
namespace Smarty\Resource;

/**
 * Smarty Resource Plugin
 * Base implementation for resource plugins that don't compile cache
 *
 */
abstract class Recompiled_Plugin extends Base_Plugin
{
    /**
     * Flag that it's an recompiled resource
     *
     * @var bool
     */
    public $recompiled = true;
    /**
     * Flag if resource does allow compilation
     */
    public function supports_compiled_templates(): bool
    {
        return false;
    }
    /*
     * Disable timestamp checks for recompiled resource.
     *
     * @return bool
     */
    /**
     * @return bool
     */
    public function check_timestamps()
    {
        return false;
    }
}