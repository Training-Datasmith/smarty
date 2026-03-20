<?php

declare (strict_types=1);
namespace Smarty\Extension;

use Smarty\Exception;
class Callback_Wrapper
{
    /**
     * @var callback
     */
    private $callback;
    /**
     * @var string
     */
    private $modifier_name;
    /**
     * @param callback $callback
     */
    public function __construct(string $modifier_name, $callback)
    {
        $this->callback = $callback;
        $this->modifier_name = $modifier_name;
    }
    public function handle(...$params)
    {
        try {
            return ($this->callback)(...$params);
        } catch (\Argument_Count_Error $e) {
            throw new Exception('Invalid number of arguments to modifier ' . $this->modifier_name);
        }
    }
}