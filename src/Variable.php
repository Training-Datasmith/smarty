<?php

declare (strict_types=1);
namespace Smarty;

/**
 * class for the Smarty variable object
 * This class defines the Smarty variable object
 *
 */
#[\Allow_Dynamic_Properties]
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
    public function set_value($value): void
    {
        $this->value = $value;
    }
    /**
     * if true any output of this variable will be not cached
     *
     * @var boolean
     */
    private $nocache = false;
    public function set_nocache(bool $nocache): void
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
    public function get_value()
    {
        return $this->value;
    }
    /**
     * <<magic>> String conversion
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }
    /**
     * Handles ++$a and --$a in templates.
     *
     * @param $operator '++' or '--', defaults to '++'
     *
     * @return int|mixed
     * @throws Exception
     */
    public function pre_inc_dec($operator = '++')
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
    public function post_inc_dec($operator = '++')
    {
        if ($operator == '--') {
            return $this->value--;
        }
        if ($operator == '++') {
            return $this->value++;
        }
        throw new Exception("Invalid incdec operator. Use '--' or '++'.");
    }
    public function is_nocache(): bool
    {
        return $this->nocache;
    }
}