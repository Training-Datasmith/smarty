<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile ForeachSection
 * Shared methods for {foreach} {section} tags
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile ForeachSection Class
 *
 */
abstract class Foreach_Section extends Base
{
    /**
     * Name of this tag
     *
     * @var string
     */
    protected $tag_name = '';
    /**
     * Valid properties of $smarty.xxx variable
     *
     * @var array
     */
    protected $name_properties = [];
    /**
     * {section} tag has no item properties
     *
     * @var array
     */
    protected $item_properties;
    /**
     * {section} tag has always name attribute
     *
     * @var bool
     */
    protected $is_named = true;
    /**
     * @var array
     */
    protected $match_results = [];
    /**
     * Preg search pattern
     *
     * @var string
     */
    private $property_preg = '';
    /**
     * Offsets in preg match result
     *
     * @var array
     */
    private $result_offsets = [];
    /**
     * Start offset
     *
     * @var int
     */
    private $start_offset = 0;
    /**
     * Scan sources for used tag attributes
     *
     *
     * @throws \Smarty\Exception
     */
    protected function scan_for_properties(array $attributes, \Smarty\Compiler\Template $compiler)
    {
        $this->property_preg = '~(';
        $this->start_offset = 1;
        $this->result_offsets = [];
        $this->match_results = ['named' => [], 'item' => []];
        if (isset($attributes['name'])) {
            $this->build_property_preg(true, $attributes);
        }
        if (isset($this->item_properties)) {
            if ($this->is_named) {
                $this->property_preg .= '|';
            }
            $this->build_property_preg(false, $attributes);
        }
        $this->property_preg .= ')\W~i';
        // Template source
        $this->match_template_source($compiler);
        // Parent template source
        $this->match_parent_template_source($compiler);
    }
    /**
     * Build property preg string
     */
    private function build_property_preg(bool $named, array $attributes): void
    {
        if ($named) {
            $this->result_offsets['named'] = $this->start_offset = $this->start_offset + 3;
            $this->property_preg .= "(([\$]smarty[.]{$this->tag_name}[.]" . ($this->tag_name === 'section' ? "|[\\[]\\s*" : '') . "){$attributes['name']}[.](";
            $properties = $this->name_properties;
        } else {
            $this->result_offsets['item'] = $this->start_offset = $this->start_offset + 2;
            $this->property_preg .= "([\$]{$attributes['item']}[@](";
            $properties = $this->item_properties;
        }
        $prop_name = reset($properties);
        while ($prop_name) {
            $this->property_preg .= "{$prop_name}";
            $prop_name = next($properties);
            if ($prop_name) {
                $this->property_preg .= '|';
            }
        }
        $this->property_preg .= '))';
    }
    /**
     * Find matches in source string
     *
     * @param string $source
     */
    private function match_property($source): void
    {
        preg_match_all($this->property_preg, $source, $match);
        foreach ($this->result_offsets as $key => $offset) {
            foreach ($match[$offset] as $m) {
                if (!empty($m)) {
                    $this->match_results[$key][smarty_strtolower_ascii($m)] = true;
                }
            }
        }
    }
    /**
     * Find matches in template source
     */
    private function match_template_source(\Smarty\Compiler\Template $compiler): void
    {
        $this->match_property($compiler->get_parser()->lex->data);
    }
    /**
     * Find matches in all parent template source
     *
     *
     * @throws \Smarty\Exception
     */
    private function match_parent_template_source(\Smarty\Compiler\Template $compiler): void
    {
        // search parent compiler template source
        $next_compiler = $compiler;
        while ($next_compiler !== $next_compiler->get_parent_compiler()) {
            $next_compiler = $next_compiler->get_parent_compiler();
            if ($compiler !== $next_compiler) {
                // get template source
                $_content = $next_compiler->get_template()->get_source()->get_content();
                if ($_content !== '') {
                    // run pre filter if required
                    $_content = $next_compiler->get_smarty()->run_pre_filters($_content, $next_compiler->get_template());
                    $this->match_property($_content);
                }
            }
        }
    }
    /**
     * Compiles code for the {$smarty.foreach.xxx} or {$smarty.section.xxx}tag
     *
     * @param \Smarty\Compiler\Template $compiler compiler object
     * @param array $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     */
    public function compile_special_variable(\Smarty\Compiler\Template $compiler, array $parameter)
    {
        $tag = smarty_strtolower_ascii(trim($parameter[0], '"\''));
        $name = isset($parameter[1]) ? $compiler->get_id($parameter[1]) : false;
        if (!$name) {
            $compiler->trigger_template_error("missing or illegal \$smarty.{$tag} name attribute", null, true);
        }
        $property = isset($parameter[2]) ? smarty_strtolower_ascii($compiler->get_id($parameter[2])) : false;
        if (!$property || !in_array($property, $this->name_properties)) {
            $compiler->trigger_template_error("missing or illegal \$smarty.{$tag} property attribute", null, true);
        }
        $tag_var = "'__smarty_{$tag}_{$name}'";
        return "(\$_smarty_tpl->getValue({$tag_var})['{$property}'] ?? null)";
    }
}