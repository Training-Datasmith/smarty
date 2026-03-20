<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

/**
 * Smarty Internal Plugin Compile Foreach Class
 *
 */
class Foreach_Tag extends Foreach_Section
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $required_attributes = ['from', 'item'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $optional_attributes = ['name', 'key', 'properties'];
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $shorttag_order = ['from', 'item', 'key', 'name'];
    /**
     * counter
     *
     * @var int
     */
    private static $counter = 0;
    /**
     * Name of this tag
     *
     * @var string
     */
    protected $tag_name = 'foreach';
    /**
     * Valid properties of $smarty.foreach.name.xxx variable
     *
     * @var array
     */
    protected $name_properties = ['first', 'last', 'index', 'iteration', 'show', 'total'];
    /**
     * Valid properties of $item@xxx variable
     *
     * @var array
     */
    protected $item_properties = ['first', 'last', 'index', 'iteration', 'show', 'total', 'key'];
    /**
     * Flag if tag had name attribute
     *
     * @var bool
     */
    protected $is_named = false;
    /**
     * Compiles code for the {foreach} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     * @throws \Smarty\Exception
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $compiler->loop_nesting++;
        // init
        $this->is_named = false;
        // check and get attributes
        $_attr = $this->get_attributes($compiler, $args);
        $from = $_attr['from'];
        $item = $compiler->get_id($_attr['item']);
        if ($item === false) {
            $item = $this->get_variable_name($_attr['item']);
        }
        $key = $name = null;
        $attributes = ['item' => $item];
        if (isset($_attr['key'])) {
            $key = $compiler->get_id($_attr['key']);
            if ($key === false) {
                $key = $this->get_variable_name($_attr['key']);
            }
            $attributes['key'] = $key;
        }
        if (isset($_attr['name'])) {
            $this->is_named = true;
            $name = $attributes['name'] = $compiler->get_id($_attr['name']);
        }
        foreach ($attributes as $a => $v) {
            if ($v === false) {
                $compiler->trigger_template_error("'{$a}' attribute/variable has illegal value", null, true);
            }
        }
        $from_name = $this->get_variable_name($_attr['from']);
        if ($from_name) {
            foreach (['item', 'key'] as $a) {
                if (isset($attributes[$a]) && $attributes[$a] === $from_name) {
                    $compiler->trigger_template_error("'{$a}' and 'from' may not have same variable name '{$from_name}'", null, true);
                }
            }
        }
        $item_var = "\$_smarty_tpl->getVariable('{$item}')";
        $local_variable_prefix = '$foreach' . self::$counter++;
        // search for used tag attributes
        $item_attr = [];
        $named_attr = [];
        $this->scan_for_properties($attributes, $compiler);
        if (!empty($this->match_results['item'])) {
            $item_attr = $this->match_results['item'];
        }
        if (!empty($this->match_results['named'])) {
            $named_attr = $this->match_results['named'];
        }
        if (isset($_attr['properties']) && preg_match_all('/[\'](.*?)[\']/', $_attr['properties'], $match)) {
            foreach ($match[1] as $prop) {
                if (in_array($prop, $this->item_properties)) {
                    $item_attr[$prop] = true;
                } else {
                    $compiler->trigger_template_error("Invalid property '{$prop}'", null, true);
                }
            }
            if ($this->is_named) {
                foreach ($match[1] as $prop) {
                    if (in_array($prop, $this->name_properties)) {
                        $name_attr[$prop] = true;
                    } else {
                        $compiler->trigger_template_error("Invalid property '{$prop}'", null, true);
                    }
                }
            }
        }
        if (isset($item_attr['first'])) {
            $item_attr['index'] = true;
        }
        if (isset($named_attr['first'])) {
            $named_attr['index'] = true;
        }
        if (isset($named_attr['last'])) {
            $named_attr['iteration'] = true;
            $named_attr['total'] = true;
        }
        if (isset($item_attr['last'])) {
            $item_attr['iteration'] = true;
            $item_attr['total'] = true;
        }
        if (isset($named_attr['show'])) {
            $named_attr['total'] = true;
        }
        if (isset($item_attr['show'])) {
            $item_attr['total'] = true;
        }
        $key_term = '';
        if (isset($attributes['key'])) {
            $key_term = "\$_smarty_tpl->getVariable('{$key}')->value => ";
        }
        if (isset($item_attr['key'])) {
            $key_term = "{$item_var}->key => ";
        }
        if ($this->is_named) {
            $foreach_var = "\$_smarty_tpl->tpl_vars['__smarty_foreach_{$attributes['name']}']";
        }
        $need_total = isset($item_attr['total']);
        if ($compiler->tag_nocache) {
            // push a {nocache} tag onto the stack to prevent caching of this block
            $this->open_tag($compiler, 'nocache');
        }
        // Register tag
        $this->open_tag($compiler, 'foreach', ['foreach', $compiler->tag_nocache, $local_variable_prefix, $item, !empty($item_attr)]);
        // generate output code
        $output = "<?php\n";
        $output .= "\$_from = \$_smarty_tpl->getSmarty()->getRuntime('Foreach')->init(\$_smarty_tpl, {$from}, " . var_export($item, true);
        if ($name || $need_total || $key) {
            $output .= ', ' . var_export($need_total, true);
        }
        if ($name || $key) {
            $output .= ', ' . var_export($key, true);
        }
        if ($name) {
            $output .= ', ' . var_export($name, true) . ', ' . var_export($named_attr, true);
        }
        $output .= ");\n";
        if (isset($item_attr['show'])) {
            $output .= "{$item_var}->show = ({$item_var}->total > 0);\n";
        }
        if (isset($item_attr['iteration'])) {
            $output .= "{$item_var}->iteration = 0;\n";
        }
        if (isset($item_attr['index'])) {
            $output .= "{$item_var}->index = -1;\n";
        }
        $output .= "{$local_variable_prefix}DoElse = true;\n";
        $output .= "foreach (\$_from ?? [] as {$key_term}{$item_var}->value) {\n";
        $output .= "{$local_variable_prefix}DoElse = false;\n";
        if (isset($attributes['key']) && isset($item_attr['key'])) {
            $output .= "\$_smarty_tpl->assign('{$key}', {$item_var}->key);\n";
        }
        if (isset($item_attr['iteration'])) {
            $output .= "{$item_var}->iteration++;\n";
        }
        if (isset($item_attr['index'])) {
            $output .= "{$item_var}->index++;\n";
        }
        if (isset($item_attr['first'])) {
            $output .= "{$item_var}->first = !{$item_var}->index;\n";
        }
        if (isset($item_attr['last'])) {
            $output .= "{$item_var}->last = {$item_var}->iteration === {$item_var}->total;\n";
        }
        if (isset($foreach_var)) {
            if (isset($named_attr['iteration'])) {
                $output .= "{$foreach_var}->value['iteration']++;\n";
            }
            if (isset($named_attr['index'])) {
                $output .= "{$foreach_var}->value['index']++;\n";
            }
            if (isset($named_attr['first'])) {
                $output .= "{$foreach_var}->value['first'] = !{$foreach_var}->value['index'];\n";
            }
            if (isset($named_attr['last'])) {
                $output .= "{$foreach_var}->value['last'] = {$foreach_var}->value['iteration'] === {$foreach_var}->value['total'];\n";
            }
        }
        if (!empty($item_attr)) {
            $output .= "{$local_variable_prefix}Backup = clone \$_smarty_tpl->getVariable('{$item}');\n";
        }
        return $output . '?>';
    }
    /**
     * Get variable name from string
     *
     * @param string $input
     *
     * @return bool|string
     */
    private function get_variable_name($input)
    {
        if (preg_match('~^[$]_smarty_tpl->getValue\([\'"]*([0-9]*[a-zA-Z_]\w*)[\'"]*\]\)$~', $input, $match)) {
            return $match[1];
        }
        return false;
    }
    /**
     * Compiles code for to restore saved template variables
     *
     * @param int $levels number of levels to restore
     *
     * @return string compiled code
     */
    public function compile_restore($levels): string
    {
        return "\$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore(\$_smarty_tpl, {$levels});";
    }
}