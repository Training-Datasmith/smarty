<?php

declare (strict_types=1);
namespace Smarty\Runtime;

use Smarty\Template;
/**
 * Foreach Runtime Methods count(), init(), restore()
 *
 * @author     Uwe Tews
 */
class Foreach_Runtime
{
    /**
     * Stack of saved variables
     *
     * @var array
     */
    private $stack = [];
    /**
     * Init foreach loop
     *  - save item and key variables, named foreach property data if defined
     *  - init item and key variables, named foreach property data if required
     *  - count total if required
     *
     * @param mixed $from values to loop over
     * @param string $item variable name
     * @param bool $needTotal flag if we need to count values
     * @param null|string $key variable name
     * @param null|string $name of named foreach
     * @param array $properties of named foreach
     * @return mixed $from
     */
    public function init(Template $tpl, $from, $item, $need_total = false, $key = null, $name = null, array $properties = [])
    {
        $need_total = $need_total || isset($properties['total']);
        $save_vars = [];
        $total = null;
        if (!is_array($from)) {
            if (is_object($from)) {
                if ($need_total) {
                    $total = $this->count($from);
                }
            } else {
                settype($from, 'array');
            }
        }
        if (!isset($total)) {
            $total = empty($from) ? 0 : ($need_total ? count($from) : 1);
        }
        if ($tpl->has_variable($item)) {
            $save_vars['item'] = [$item, $tpl->get_variable($item)->get_value()];
        }
        $tpl->assign($item);
        if ($total === 0) {
            $from = null;
        } else if ($key) {
            if ($tpl->has_variable($key)) {
                $save_vars['key'] = [$key, clone $tpl->get_variable($key)];
            }
            $tpl->assign($key);
        }
        if ($need_total) {
            $tpl->get_variable($item)->total = $total;
        }
        if ($name) {
            $named_var = "__smarty_foreach_{$name}";
            if ($tpl->has_variable($named_var)) {
                $save_vars['named'] = [$named_var, clone $tpl->get_variable($named_var)];
            }
            $named_prop = [];
            if (isset($properties['total'])) {
                $named_prop['total'] = $total;
            }
            if (isset($properties['iteration'])) {
                $named_prop['iteration'] = 0;
            }
            if (isset($properties['index'])) {
                $named_prop['index'] = -1;
            }
            if (isset($properties['show'])) {
                $named_prop['show'] = $total > 0;
            }
            $tpl->assign($named_var, $named_prop);
        }
        $this->stack[] = $save_vars;
        return $from;
    }
    /**
     * [util function] counts an array, arrayAccess/traversable or PDOStatement object
     *
     * @param mixed $value
     *
     * @return int   the count for arrays and objects that implement countable, 1 for other objects that don't, and 0
     *               for empty elements
     * @throws \Exception
     */
    public function count($value): int
    {
        if ($value instanceof \IteratorAggregate) {
            // Note: getIterator() returns a Traversable, not an Iterator
            // thus rewind() and valid() methods may not be present
            return iterator_count($value->getIterator());
        }
        if ($value instanceof \Iterator) {
            return $value instanceof \Generator ? 1 : iterator_count($value);
        }
        if ($value instanceof \Countable) {
            return count($value);
        }
        return count((array) $value);
    }
    /**
     * Restore saved variables
     *
     * will be called by {break n} or {continue n} for the required number of levels
     *
     * @param int $levels number of levels
     */
    public function restore(Template $tpl, $levels = 1): void
    {
        while ($levels) {
            $save_vars = array_pop($this->stack);
            if (!empty($save_vars)) {
                if (isset($save_vars['item'])) {
                    $tpl->get_variable($save_vars['item'][0])->set_value($save_vars['item'][1]);
                }
                if (isset($save_vars['key'])) {
                    $tpl->set_variable($save_vars['key'][0], $save_vars['key'][1]);
                }
                if (isset($save_vars['named'])) {
                    $tpl->set_variable($save_vars['named'][0], $save_vars['named'][1]);
                }
            }
            $levels--;
        }
    }
}