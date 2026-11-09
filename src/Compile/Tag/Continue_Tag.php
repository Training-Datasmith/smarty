<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Continue
 * Compiles the {continue} tag
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

/**
 * Smarty Internal Plugin Compile Continue Class
 *
 */
class Continue_Tag extends Break_Tag
{
    /**
     * Tag name
     *
     * @var string
     */
    protected $tag = 'continue';
}