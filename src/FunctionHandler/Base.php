<?php

declare (strict_types=1);
namespace Smarty\Function_Handler;

use Smarty\Template;
class Base implements Function_Handler_Interface
{
    /**
     * @var bool
     */
    protected $cacheable = true;
    public function is_cacheable(): bool
    {
        return $this->cacheable;
    }
    public function handle($params, Template $template): void
    {
        // TODO: Implement handle() method.
    }
}