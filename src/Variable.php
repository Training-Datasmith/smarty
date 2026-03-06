<?php

declare(strict_types=1);

namespace Smarty;

/**
 * class for the Smarty variable object
 * This class defines the Smarty variable object
 *


 */
#[\AllowDynamicProperties]
class Variable
{
    /**
     * template variable
     *
     * @var mixed
     */
    public $value;

    /**
     * Other r/w properties for foreach, for, while, etc.
     */
    public $step;
    public $total;
    public $first;
    public $last;
    public $key;
    public $show;
    public $iteration;
    public $index;

    /**
     * @param mixed|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * if true any output of this variable will be not cached
     *
     * @var boolean
     */
    private $nocache = false;

    public function setNocache(bool $nocache): void
    {
        $this->nocache = $nocache;
    }

    /**
     * create Smarty variable object
     *
     * @param mixed   $value   the value to assign
     * @param boolean $nocache if true any output of this variable will be not cached
     */
    public function __construct($value = null, $nocache = false)
    {
        $this->value = $value;
        $this->nocache = $nocache;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * <<magic>> String conversion
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * Handles ++$a and --$a in templates.
     *
     * @param $operator '++' or '--', defaults to '++'
     *
     * @return int|mixed
     * @throws Exception
     */
    public function preIncDec($operator = '++')
    {
        if ($operator == '--') {
            return --$this->value;
        }
        if ($operator == '++') {
            return ++$this->value;
        }
        throw new Exception("Invalid incdec operator. Use '--' or '++'.");
    }

    /**
     * Handles $a++ and $a-- in templates.
     *
     * @param $operator '++' or '--', defaults to '++'
     *
     * @return int|mixed
     * @throws Exception
     */
    public function postIncDec($operator = '++')
    {
        if ($operator == '--') {
            return $this->value--;
        }
        if ($operator == '++') {
            return $this->value++;
        }
        throw new Exception("Invalid incdec operator. Use '--' or '++'.");
    }

    public function isNocache(): bool
    {
        return $this->nocache;
    }

}
