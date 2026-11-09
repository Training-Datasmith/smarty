<?php

declare (strict_types=1);
namespace Smarty\Function_Handler;

use Smarty\Template;
class Bc_Plugin_Wrapper extends Base
{
    private $callback;
    public function __construct($callback, bool $cacheable = true)
    {
        $this->callback = $callback;
        $this->cacheable = $cacheable;
    }
    public function handle($params, Template $template)
    {
        $func = $this->callback;
        return $func($params, $template);
    }
}