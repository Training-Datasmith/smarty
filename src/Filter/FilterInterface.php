<?php

declare(strict_types=1);

namespace Smarty\Filter;

interface FilterInterface
{
    public function filter($code, \Smarty\Template $template);

}
