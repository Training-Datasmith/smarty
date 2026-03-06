<?php

declare(strict_types=1);

namespace Smarty\FunctionHandler;

use Smarty\Template;

interface FunctionHandlerInterface
{
    public function handle($params, Template $template);
    public function isCacheable(): bool;
}
