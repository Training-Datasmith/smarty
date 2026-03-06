<?php

declare(strict_types=1);

namespace Smarty\Extension;

use Smarty\Exception;

class CallbackWrapper
{
    /**
     * @var callback
     */
    private $callback;
    /**
     * @var string
     */
    private $modifierName;

    /**
     * @param callback $callback
     */
    public function __construct(string $modifierName, $callback)
    {
        $this->callback = $callback;
        $this->modifierName = $modifierName;
    }

    public function handle(...$params)
    {
        try {
            return ($this->callback)(...$params);
        } catch (\ArgumentCountError $e) {
            throw new Exception('Invalid number of arguments to modifier ' . $this->modifierName);
        }
    }

}
