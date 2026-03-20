<?php

declare (strict_types=1);
namespace Smarty\Block_Handler;

use Smarty\Template;
abstract class Base implements Block_Handler_Interface
{
    /**
     * @var bool
     */
    protected $cacheable = true;
    abstract public function handle($params, $content, Template $template, &$repeat);
    public function is_cacheable(): bool
    {
        return $this->cacheable;
    }
}