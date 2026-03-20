<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Smarty Template Compiler Base
 * This file contains the basic classes and methods for compiling Smarty templates with lexer/parser
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compiler;

use function array_merge;
use function is_array;
use Smarty\Compile\Block_Compiler;
use Smarty\Compile\Default_Handler_Block_Compiler;
use Smarty\Compile\Default_Handler_Function_Call_Compiler;
use Smarty\Compile\Function_Call_Compiler;
use Smarty\Compile\Modifier_Compiler;
use Smarty\Compile\Object_Method_Block_Compiler;
use Smarty\Compile\Object_Method_Call_Compiler;
use Smarty\Compile\Print_Expression_Compiler;
use Smarty\Compiler_Exception;
use Smarty\Exception;
use Smarty\Lexer\Template_Lexer;
use Smarty\Parser\Template_Parser;
use Smarty\Smarty;
use function strlen;
use function substr;
/**
 * Class SmartyTemplateCompiler
 *
 */
class Template extends Base_Compiler
{
    /**
     * counter for prefix variable number
     *
     * @var int
     */
    public static $prefix_variable_number = 0;
    /**
     * Parser object
     *
     * @var \Smarty\Parser\TemplateParser
     */
    private $parser;
    /**
     * hash for nocache sections
     *
     * @var mixed
     */
    public $nocache_hash;
    /**
     * suppress generation of nocache code
     *
     * @var bool
     */
    public $suppress_nocache_processing = false;
    /**
     * caching enabled (copied from template object)
     *
     * @var int
     */
    public $caching = 0;
    /**
     * tag stack
     *
     * @var array
     */
    private $_tag_stack = [];
    /**
     * tag stack count
     *
     * @var array
     */
    private $_tag_stack_count = [];
    /**
     * current template
     *
     * @var \Smarty\Template
     */
    private $template;
    /**
     * merged included sub template data
     *
     * @var array
     */
    public $merged_sub_templates_data = [];
    /**
     * merged sub template code
     *
     * @var array
     */
    public $merged_sub_templates_code = [];
    /**
     * source line offset for error messages
     *
     * @var int
     */
    public $trace_line_offset = 0;
    /**
     * trace uid
     *
     * @var string
     */
    public $trace_uid = '';
    /**
     * trace file path
     *
     * @var string
     */
    public $trace_filepath = '';
    /**
     * Template functions
     *
     * @var array
     */
    public $tpl_function = [];
    /**
     * compiled template or block function code
     *
     * @var string
     */
    public $block_or_function_code = '';
    /**
     * flags for used modifier plugins
     *
     * @var array
     */
    public $modifier_plugins = [];
    /**
     * parent compiler object for merged subtemplates and template functions
     *
     * @var \Smarty\Compiler\Template
     */
    private $parent_compiler;
    /**
     * Flag true when compiling nocache section
     *
     * @var bool
     */
    public $nocache = false;
    /**
     * Flag true when tag is compiled as nocache
     *
     * @var bool
     */
    public $tag_nocache = false;
    /**
     * Compiled tag prefix code
     *
     * @var array
     */
    public $prefix_code = [];
    /**
     * Prefix code  stack
     *
     * @var array
     */
    public $prefix_code_stack = [];
    /**
     * A variable string was compiled
     *
     * @var bool
     */
    public $has_variable_string = false;
    /**
     * Stack for {setfilter} {/setfilter}
     *
     * @var array
     */
    public $variable_filter_stack = [];
    /**
     * Nesting count of looping tags like {foreach}, {for}, {section}, {while}
     *
     * @var int
     */
    public $loop_nesting = 0;
    /**
     * Strip preg pattern
     *
     * @var string
     */
    public $strip_reg_ex = '![\t ]*[\r\n]+[\t ]*!';
    /**
     * General storage area for tag compiler plugins
     *
     * @var array
     */
    public $_cache = [];
    /**
     * Lexer preg pattern for left delimiter
     *
     * @var string
     */
    private $ldel_preg = '[{]';
    /**
     * Lexer preg pattern for right delimiter
     *
     * @var string
     */
    private $rdel_preg = '[}]';
    /**
     * Length of right delimiter
     *
     * @var int
     */
    private $rdel_length = 0;
    /**
     * Length of left delimiter
     *
     * @var int
     */
    private $ldel_length = 0;
    /**
     * Lexer preg pattern for user literals
     *
     * @var string
     */
    private $literal_preg = '';
    /**
     * array of callbacks called when the normal compile process of template is finished
     *
     * @var array
     */
    public $post_compile_callbacks = [];
    /**
     * prefix code
     *
     * @var string
     */
    public $prefix_compiled_code = '';
    /**
     * postfix code
     *
     * @var string
     */
    public $postfix_compiled_code = '';
    /**
     * @var ObjectMethodBlockCompiler
     */
    private $object_method_block_compiler;
    /**
     * @var DefaultHandlerBlockCompiler
     */
    private $default_handler_block_compiler;
    /**
     * @var BlockCompiler
     */
    private $block_compiler;
    /**
     * @var DefaultHandlerFunctionCallCompiler
     */
    private $default_handler_function_call_compiler;
    /**
     * @var FunctionCallCompiler
     */
    private $function_call_compiler;
    /**
     * @var ObjectMethodCallCompiler
     */
    private $object_method_call_compiler;
    /**
     * @var ModifierCompiler
     */
    private $modifier_compiler;
    /**
     * @var PrintExpressionCompiler
     */
    private $print_expression_compiler;
    /**
     * Depth of nested {nocache}{/nocache} blocks. If outside, this is 0. If inside, this is 1 or higher (if nested).
     * @var int
     */
    private $no_cache_stack_depth = 0;
    /**
     * disabled auto-escape (when set to true, the next variable output is not auto-escaped)
     *
     * @var boolean
     */
    private $raw_output = false;
    /**
     * Initialize compiler
     *
     * @param Smarty $smarty global instance
     */
    public function __construct(Smarty $smarty)
    {
        $this->smarty = $smarty;
        $this->nocache_hash = str_replace(['.', ','], '_', bin2hex(random_bytes(16)));
        $this->modifier_compiler = new Modifier_Compiler();
        $this->function_call_compiler = new Function_Call_Compiler();
        $this->default_handler_function_call_compiler = new Default_Handler_Function_Call_Compiler();
        $this->block_compiler = new Block_Compiler();
        $this->default_handler_block_compiler = new Default_Handler_Block_Compiler();
        $this->object_method_block_compiler = new Object_Method_Block_Compiler();
        $this->object_method_call_compiler = new Object_Method_Call_Compiler();
        $this->print_expression_compiler = new Print_Expression_Compiler();
    }
    /**
     * Method to compile a Smarty template
     *
     * @param \Smarty\Template $template template object to compile
     *
     * @return string code
     * @throws Exception
     */
    public function compile_template(\Smarty\Template $template)
    {
        return $template->create_code_frame($this->compile_template_source($template), $this->smarty->run_post_filters($this->block_or_function_code, $this->template) . join('', $this->merged_sub_templates_code), false, $this);
    }
    /**
     * Compile template source and run optional post filter
     *
     * @param \Smarty\Template $template
     *
     * @return string
     * @throws CompilerException
     * @throws Exception
     */
    public function compile_template_source(\Smarty\Template $template, ?\Smarty\Compiler\Template $parent_compiler = null)
    {
        try {
            // save template object in compiler class
            $this->template = $template;
            if ($this->smarty->debugging) {
                $this->smarty->get_debug()->start_compile($this->template);
            }
            $this->parent_compiler = $parent_compiler ?: $this;
            if (empty($template->get_compiled()->nocache_hash)) {
                $template->get_compiled()->nocache_hash = $this->nocache_hash;
            } else {
                $this->nocache_hash = $template->get_compiled()->nocache_hash;
            }
            $this->caching = $template->caching;
            // flag for nocache sections
            $this->nocache = false;
            $this->tag_nocache = false;
            // reset has nocache code flag
            $this->template->get_compiled()->set_nocache_code(false);
            $this->has_variable_string = false;
            $this->prefix_code = [];
            // add file dependency
            if ($this->template->get_source()->handler->check_timestamps()) {
                $this->parent_compiler->get_template()->get_compiled()->file_dependency[$this->template->get_source()->uid] = [$this->template->get_source()->get_resource_name(), $this->template->get_source()->get_time_stamp(), $this->template->get_source()->type];
            }
            // get template source
            if (!empty($this->template->get_source()->components)) {
                $_compiled_code = '<?php $_smarty_tpl->getInheritance()->init($_smarty_tpl, true); ?>';
                $i = 0;
                $reversed_components = array_reverse($this->template->get_source()->components);
                foreach ($reversed_components as $source) {
                    $i++;
                    if ($i === count($reversed_components)) {
                        $_compiled_code .= '<?php $_smarty_tpl->getInheritance()->endChild($_smarty_tpl); ?>';
                    }
                    $_compiled_code .= $this->compile_tag('include', [var_export($source->resource, true), ['scope' => 'parent']]);
                }
                $_compiled_code = $this->smarty->run_post_filters($_compiled_code, $this->template);
            } else {
                // get template source
                $_content = $this->template->get_source()->get_content();
                $_compiled_code = $this->smarty->run_post_filters($this->do_compile($this->smarty->run_pre_filters($_content, $this->template), true), $this->template);
            }
        } catch (\Exception $e) {
            if ($this->smarty->debugging) {
                $this->smarty->get_debug()->end_compile($this->template);
            }
            $this->_tag_stack = [];
            // free memory
            $this->parent_compiler = null;
            $this->template = null;
            $this->parser = null;
            throw $e;
        }
        if ($this->smarty->debugging) {
            $this->smarty->get_debug()->end_compile($this->template);
        }
        $this->parent_compiler = null;
        $this->parser = null;
        return $_compiled_code;
    }
    /**
     * Compile Tag
     * This is a call back from the lexer/parser
     *
     * Save current prefix code
     * Compile tag
     * Merge tag prefix code with saved one
     * (required nested tags in attributes)
     *
     * @param string $tag tag name
     * @param array $args array with tag attributes
     * @param array $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws Exception
     * @throws CompilerException
     */
    public function compile_tag($tag, $args, $parameter = [])
    {
        $this->prefix_code_stack[] = $this->prefix_code;
        $this->prefix_code = [];
        $result = $this->compile_tag2($tag, $args, $parameter);
        $this->prefix_code = array_merge($this->prefix_code, array_pop($this->prefix_code_stack));
        return $result;
    }
    /**
     * Compiles code for modifier execution
     *
     * @param $modifierlist
     * @param $value
     *
     * @return string compiled code
     * @throws CompilerException
     * @throws Exception
     */
    public function compile_modifier($modifierlist, $value): string
    {
        return $this->modifier_compiler->compile([], $this, ['modifierlist' => $modifierlist, 'value' => $value]);
    }
    /**
     * compile variable
     *
     * @param string $variable
     */
    public function trigger_tag_no_cache($variable): void
    {
        if (!strpos($variable, '(')) {
            // not a variable variable
            $var = trim($variable, '\'');
            $this->tag_nocache = $this->tag_nocache || $this->template->get_variable($var, true, false)->is_nocache();
        }
    }
    /**
     * compile config variable
     *
     *
     */
    public function compile_config_variable(string $variable): string
    {
        // return '$_smarty_tpl->config_vars[' . $variable . ']';
        return '$_smarty_tpl->getConfigVariable(' . $variable . ')';
    }
    /**
     * This method is called from parser to process a text content section if strip is enabled
     * - remove text from inheritance child templates as they may generate output
     *
     * @param string $text
     *
     * @return string
     */
    public function process_text($text)
    {
        if (strpos($text, '<') === false) {
            return preg_replace($this->strip_reg_ex, '', $text);
        }
        $store = [];
        $_store = 0;
        // capture html elements not to be messed with
        $_offset = 0;
        if (preg_match_all('#(<script[^>]*>.*?</script[^>]*>)|(<textarea[^>]*>.*?</textarea[^>]*>)|(<pre[^>]*>.*?</pre[^>]*>)#is', $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $store[] = $match[0][0];
                $_length = strlen($match[0][0]);
                $replace = '@!@SMARTY:' . $_store . ':SMARTY@!@';
                $text = substr_replace($text, $replace, $match[0][1] - $_offset, $_length);
                $_offset += $_length - strlen($replace);
                $_store++;
            }
        }
        $expressions = [
            // replace multiple spaces between tags by a single space
            '#(:SMARTY@!@|>)[\040\011]+(?=@!@SMARTY:|<)#s' => '\1 \2',
            // remove newline between tags
            '#(:SMARTY@!@|>)[\040\011]*[\n]\s*(?=@!@SMARTY:|<)#s' => '\1\2',
            // remove multiple spaces between attributes (but not in attribute values!)
            '#(([a-z0-9]\s*=\s*("[^"]*?")|(\'[^\']*?\'))|<[a-z0-9_]+)\s+([a-z/>])#is' => '\1 \5',
            '#>[\040\011]+$#Ss' => '> ',
            '#>[\040\011]*[\n]\s*$#Ss' => '>',
            $this->strip_reg_ex => '',
        ];
        $text = preg_replace(array_keys($expressions), array_values($expressions), $text);
        $_offset = 0;
        if (preg_match_all('#@!@SMARTY:([0-9]+):SMARTY@!@#is', $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $_length = strlen($match[0][0]);
                $replace = $store[$match[1][0]];
                $text = substr_replace($text, $replace, $match[0][1] + $_offset, $_length);
                $_offset += strlen($replace) - $_length;
                $_store++;
            }
        }
        return $text;
    }
    /**
     * lazy loads internal compile plugin for tag compile objects cached for reuse.
     *
     * class name format:  \Smarty\Compile\TagName
     *
     * @param string $tag tag name
     *
     * @return ?\Smarty\Compile\CompilerInterface tag compiler object or null if not found or untrusted by security policy
     */
    public function get_tag_compiler($tag): ?\Smarty\Compile\Compiler_Interface
    {
        $tag = strtolower($tag);
        if (isset($this->smarty->security_policy) && !$this->smarty->security_policy->is_trusted_tag($tag, $this)) {
            return null;
        }
        foreach ($this->smarty->get_extensions() as $extension) {
            if ($compiler = $extension->get_tag_compiler($tag)) {
                return $compiler;
            }
        }
        return null;
    }
    /**
     * lazy loads internal compile plugin for modifier compile objects cached for reuse.
     *
     * @param string $modifier tag name
     *
     * @return bool|\Smarty\Compile\Modifier\ModifierCompilerInterface tag compiler object or false if not found or untrusted by security policy
     */
    public function get_modifier_compiler($modifier)
    {
        if (isset($this->smarty->security_policy) && !$this->smarty->security_policy->is_trusted_modifier($modifier, $this)) {
            return false;
        }
        foreach ($this->smarty->get_extensions() as $extension) {
            if ($modifier_compiler = $extension->get_modifier_compiler($modifier)) {
                return $modifier_compiler;
            }
        }
        return false;
    }
    /**
     * Check for plugins by default plugin handler
     *
     * @param string $tag name of tag
     * @param string $plugin_type type of plugin
     *
     * @return callback|null
     * @throws \Smarty\CompilerException
     */
    public function get_plugin_from_default_handler($tag, $plugin_type)
    {
        $default_plugin_handler_func = $this->smarty->get_default_plugin_handler_func();
        if (!is_callable($default_plugin_handler_func)) {
            return null;
        }
        $callback = null;
        $script = null;
        $cacheable = true;
        $result = \call_user_func_array($default_plugin_handler_func, [
            $tag,
            $plugin_type,
            null,
            // This used to pass $this->template, but this parameter has been removed in 5.0
            &$callback,
            &$script,
            &$cacheable,
        ]);
        if ($result) {
            $this->tag_nocache = $this->tag_nocache || !$cacheable;
            if ($script !== null) {
                if (is_file($script)) {
                    include_once $script;
                } else {
                    $this->trigger_template_error("Default plugin handler: Returned script file '{$script}' for '{$tag}' not found");
                }
            }
            if (is_callable($callback)) {
                return $callback;
            }
            $this->trigger_template_error("Default plugin handler: Returned callback for '{$tag}' not callable");
        }
        return null;
    }
    /**
     * Append code segments and remove unneeded ?> <?php transitions
     *
     *
     */
    public function append_code(string $left, string $right): string
    {
        if (preg_match('/\s*\?>\s?$/D', $left) && preg_match('/^<\?php\s+/', $right)) {
            $left = preg_replace('/\s*\?>\s?$/D', "\n", $left);
            $left .= preg_replace('/^<\?php\s+/', '', $right);
        } else {
            $left .= $right;
        }
        return $left;
    }
    /**
     * Inject inline code for nocache template sections
     * This method gets the content of each template element from the parser.
     * If the content is compiled code, and it should be not be cached the code is injected
     * into the rendered output.
     *
     * @param string $content content of template element
     *
     * @return string  content
     */
    public function process_nocache_code($content)
    {
        // If the template is not evaluated, and we have a nocache section and/or a nocache tag
        // generate replacement code
        if (!empty($content) && !$this->template->get_source()->handler->recompiled && $this->caching && $this->is_nocache_active()) {
            $this->template->get_compiled()->set_nocache_code(true);
            $_output = addcslashes($content, '\'\\');
            $_output = "<?php echo '" . $this->get_nocache_block_start_marker() . $_output . $this->get_nocache_block_end_marker() . "';?>\n";
        } else {
            $_output = $content;
        }
        $this->modifier_plugins = [];
        $this->suppress_nocache_processing = false;
        $this->tag_nocache = false;
        return $_output;
    }
    private function get_nocache_block_start_marker(): string
    {
        return "/*%%SmartyNocache:{$this->nocache_hash}%%*/";
    }
    private function get_nocache_block_end_marker(): string
    {
        return "/*/%%SmartyNocache:{$this->nocache_hash}%%*/";
    }
    /**
     * Get Id
     *
     * @param string $input
     *
     * @return bool|string
     */
    public function get_id($input)
    {
        if (preg_match('~^([\'"]*)([0-9]*[a-zA-Z_]\w*)\1$~', $input, $match)) {
            return $match[2];
        }
        return false;
    }
    /**
     * Set nocache flag in variable or create new variable
     *
     * @param string $varName
     */
    public function set_nocache_in_variable($var_name): void
    {
        // create nocache var to make it know for further compiling
        if ($_var = $this->get_id($var_name)) {
            if ($this->template->has_variable($_var)) {
                $this->template->get_variable($_var)->set_nocache(true);
            } else {
                $this->template->assign($_var, null, true);
            }
        }
    }
    /**
     * display compiler error messages without dying
     * If parameter $args is empty it is a parser detected syntax error.
     * In this case the parser is called to obtain information about expected tokens.
     * If parameter $args contains a string this is used as error message
     *
     * @param string $args individual error message or null
     * @param string $line line-number
     * @param null|bool $tagline if true the line number of last tag
     *
     * @throws \Smarty\CompilerException when an unexpected token is found
     */
    public function trigger_template_error($args = null, $line = null, $tagline = null): void
    {
        $lex = $this->parser->lex;
        if ($tagline === true) {
            // get line number of Tag
            $line = $lex->taglineno;
        } elseif (!isset($line)) {
            // get template source line which has error
            $line = $lex->line;
        } else {
            $line = (int) $line;
        }
        if (in_array($this->template->get_source()->type, ['eval', 'string'])) {
            $template_name = $this->template->get_source()->type . ':' . trim(preg_replace('![\t\r\n]+!', ' ', strlen($lex->data) > 40 ? substr($lex->data, 0, 40) . '...' : $lex->data));
        } else {
            $template_name = $this->template->get_source()->get_full_resource_name();
        }
        //        $line += $this->trace_line_offset;
        $match = preg_split("/\n/", $lex->data);
        $error_text = 'Syntax error in template "' . (empty($this->trace_filepath) ? $template_name : $this->trace_filepath) . '"  on line ' . ($line + $this->trace_line_offset) . ' "' . trim(preg_replace('![\t\r\n]+!', ' ', $match[$line - 1])) . '" ';
        if (isset($args)) {
            // individual error message
            $error_text .= $args;
        } else {
            $expect = [];
            // expected token from parser
            $error_text .= ' - Unexpected "' . $lex->value . '"';
            if (count($this->parser->yy_get_expected_tokens($this->parser->yymajor)) <= 4) {
                foreach ($this->parser->yy_get_expected_tokens($this->parser->yymajor) as $token) {
                    $exp_token = $this->parser->yy_token_name[$token];
                    if (isset($lex->smarty_token_names[$exp_token])) {
                        // token type from lexer
                        $expect[] = '"' . $lex->smarty_token_names[$exp_token] . '"';
                    } else {
                        // otherwise internal token name
                        $expect[] = $this->parser->yy_token_name[$token];
                    }
                }
                $error_text .= ', expected one of: ' . implode(' , ', $expect);
            }
        }
        if ($this->smarty->_parserdebug) {
            $this->parser->error_run_down();
            echo ob_get_clean();
            flush();
        }
        $e = new Compiler_Exception($error_text, 0, $this->template->get_source()->get_filepath() ?? $this->template->get_source()->get_full_resource_name(), $line);
        $e->source = trim(preg_replace('![\t\r\n]+!', ' ', $match[$line - 1]));
        $e->desc = $args;
        $e->template = $this->template->get_source()->get_full_resource_name();
        throw $e;
    }
    /**
     * Return var_export() value with all white spaces removed
     *
     * @param mixed $value
     *
     * @return string
     */
    public function get_var_export($value): ?string
    {
        return preg_replace('/\s/', '', var_export($value, true));
    }
    /**
     *  enter double quoted string
     *  - save tag stack count
     */
    public function enter_double_quote(): void
    {
        array_push($this->_tag_stack_count, $this->get_tag_stack_count());
    }
    /**
     * Return tag stack count
     */
    public function get_tag_stack_count(): int
    {
        return count($this->_tag_stack);
    }
    /**
     * @param $lexerPreg
     *
     * @return mixed
     */
    public function replace_delimiter($lexer_preg)
    {
        return str_replace(['SMARTYldel', 'SMARTYliteral', 'SMARTYrdel', 'SMARTYautoliteral', 'SMARTYal'], [$this->ldel_preg, $this->literal_preg, $this->rdel_preg, $this->smarty->get_auto_literal() ? '{1,}' : '{9}', $this->smarty->get_auto_literal() ? '' : '\s*'], $lexer_preg);
    }
    /**
     * Build lexer regular expressions for left and right delimiter and user defined literals
     */
    public function init_delimiter_preg(): void
    {
        $ldel = $this->smarty->get_left_delimiter();
        $this->ldel_length = strlen($ldel);
        $this->ldel_preg = '';
        foreach (str_split($ldel, 1) as $chr) {
            $this->ldel_preg .= '[' . preg_quote($chr, '/') . ']';
        }
        $rdel = $this->smarty->get_right_delimiter();
        $this->rdel_length = strlen($rdel);
        $this->rdel_preg = '';
        foreach (str_split($rdel, 1) as $chr) {
            $this->rdel_preg .= '[' . preg_quote($chr, '/') . ']';
        }
        $literals = $this->smarty->get_literals();
        if (!empty($literals)) {
            foreach ($literals as $key => $literal) {
                $literal_preg = '';
                foreach (str_split($literal, 1) as $chr) {
                    $literal_preg .= '[' . preg_quote($chr, '/') . ']';
                }
                $literals[$key] = $literal_preg;
            }
            $this->literal_preg = '|' . implode('|', $literals);
        } else {
            $this->literal_preg = '';
        }
    }
    /**
     *  leave double quoted string
     *  - throw exception if block in string was not closed
     *
     * @throws \Smarty\CompilerException
     */
    public function leave_double_quote(): void
    {
        if (array_pop($this->_tag_stack_count) !== $this->get_tag_stack_count()) {
            $tag = $this->get_open_block_tag();
            $this->trigger_template_error("unclosed '{{$tag}}' in doubled quoted string", null, true);
        }
    }
    /**
     * Get left delimiter preg
     *
     * @return string
     */
    public function get_ldel_preg()
    {
        return $this->ldel_preg;
    }
    /**
     * Get right delimiter preg
     *
     * @return string
     */
    public function get_rdel_preg()
    {
        return $this->rdel_preg;
    }
    /**
     * Get length of left delimiter
     *
     * @return int
     */
    public function get_ldel_length()
    {
        return $this->ldel_length;
    }
    /**
     * Get length of right delimiter
     *
     * @return int
     */
    public function get_rdel_length()
    {
        return $this->rdel_length;
    }
    /**
     * Get name of current open block tag
     *
     * @return string|boolean
     */
    public function get_open_block_tag()
    {
        $tag_count = $this->get_tag_stack_count();
        if ($tag_count) {
            return $this->_tag_stack[$tag_count - 1][0];
        }
        return false;
    }
    /**
     * Check if $value contains variable elements
     *
     * @param mixed $value
     *
     * @return bool|int
     */
    public function is_variable($value)
    {
        if (is_string($value)) {
            return preg_match('/[$(]/', $value);
        }
        if (is_bool($value) || is_numeric($value)) {
            return false;
        }
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if ($this->is_variable($k) || $this->is_variable($v)) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }
    /**
     * Get new prefix variable name
     *
     * @return string
     */
    public function get_new_prefix_variable()
    {
        ++self::$prefix_variable_number;
        return $this->get_prefix_variable();
    }
    /**
     * Get current prefix variable name
     */
    public function get_prefix_variable(): string
    {
        return '$_prefixVariable' . self::$prefix_variable_number;
    }
    /**
     * append  code to prefix buffer
     *
     * @param string $code
     */
    public function append_prefix_code($code): void
    {
        $this->prefix_code[] = $code;
    }
    /**
     * get prefix code string
     */
    public function get_prefix_code(): string
    {
        $code = '';
        $prefix_array = array_merge($this->prefix_code, array_pop($this->prefix_code_stack));
        $this->prefix_code_stack[] = [];
        foreach ($prefix_array as $c) {
            $code = $this->append_code($code, (string) $c);
        }
        $this->prefix_code = [];
        return $code;
    }
    public function c_style_comment($string): string
    {
        return '/*' . str_replace('*/', '* /', $string) . '*/';
    }
    public function compile_child_block()
    {
        return $this->block_compiler->compile_child($this);
    }
    public function compile_parent_block()
    {
        return $this->block_compiler->compile_parent($this);
    }
    /**
     * Compile Tag
     *
     * @param string $tag tag name
     * @param array $args array with tag attributes
     * @param array $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws Exception
     * @throws CompilerException
     */
    private function compile_tag2(string $tag, array $args, array $parameter)
    {
        // $args contains the attributes parsed and compiled by the lexer/parser
        $this->handle_nocache_flag($args);
        // compile built-in tags
        if ($tag_compiler = $this->get_tag_compiler($tag)) {
            if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->is_trusted_tag($tag, $this)) {
                $this->tag_nocache = $this->tag_nocache | !$tag_compiler->is_cacheable();
                $_output = $tag_compiler->compile($args, $this, $parameter);
                if (!empty($parameter['modifierlist'])) {
                    throw new Compiler_Exception('No modifiers allowed on ' . $tag);
                }
                return $_output;
            }
        }
        // call to function previously defined by {function} tag
        if ($this->can_compile_template_function_call($tag)) {
            if (!empty($parameter['modifierlist'])) {
                throw new Compiler_Exception('No modifiers allowed on ' . $tag);
            }
            $args['_attr']['name'] = "'{$tag}'";
            $tag_compiler = $this->get_tag_compiler('call');
            return $tag_compiler === null ? false : $tag_compiler->compile($args, $this, $parameter);
        }
        // remaining tastes: (object-)function, (object-function-)block, custom-compiler
        // opening and closing tags for these are handled with the same handler
        $base_tag = $this->get_base_tag($tag);
        // check if tag is a registered object
        if (isset($this->smarty->registered_objects[$base_tag]) && isset($parameter['object_method'])) {
            return $this->compile_registered_object_method_call($base_tag, $args, $parameter, $tag);
        }
        // check if tag is a function
        if ($this->smarty->get_function_handler($tag)) {
            if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->is_trusted_tag($tag, $this)) {
                return (new \Smarty\Compile\Print_Expression_Compiler())->compile(
                    ['nofilter'],
                    // functions are never auto-escaped
                    $this,
                    ['value' => $this->compile_function_call($tag, $args, $parameter)]
                );
            }
        }
        // check if tag is a block
        if ($this->smarty->get_block_handler($base_tag)) {
            if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->is_trusted_tag($base_tag, $this)) {
                return $this->block_compiler->compile($args, $this, $parameter, $tag, $base_tag);
            }
        }
        // the default plugin handler is a handler of last resort, it may also handle not specifically registered tags.
        if ($callback = $this->get_plugin_from_default_handler($tag, Smarty::PLUGIN_COMPILER)) {
            if (!empty($parameter['modifierlist'])) {
                throw new Compiler_Exception('No modifiers allowed on ' . $tag);
            }
            $tag_compiler = new \Smarty\Compile\Tag\Bc_Plugin_Wrapper($callback);
            return $tag_compiler->compile($args, $this, $parameter);
        }
        if ($this->get_plugin_from_default_handler($base_tag, Smarty::PLUGIN_FUNCTION)) {
            return $this->default_handler_function_call_compiler->compile($args, $this, $parameter, $tag, $tag);
        }
        if ($this->get_plugin_from_default_handler($base_tag, Smarty::PLUGIN_BLOCK)) {
            return $this->default_handler_block_compiler->compile($args, $this, $parameter, $tag, $base_tag);
        }
        $this->trigger_template_error("unknown tag '{$tag}'", null, true);
    }
    /**
     * Sets $this->tag_nocache if attributes contain the 'nocache' flag.
     *
     *
     */
    private function handle_nocache_flag(array $attributes): void
    {
        foreach ($attributes as $value) {
            if (is_string($value) && trim($value, '\'" ') == 'nocache') {
                $this->tag_nocache = true;
            }
        }
    }
    private function get_base_tag(string $tag)
    {
        if (strlen($tag) < 6 || substr($tag, -5) !== 'close') {
            return $tag;
        }
        return substr($tag, 0, -5);
    }
    /**
     * Compiles the output of a variable or expression.
     *
     * @param $value
     * @param $attributes
     * @param $modifiers
     *
     * @throws Exception
     */
    public function compile_print_expression($value, $attributes = [], $modifiers = null): string
    {
        $this->handle_nocache_flag($attributes);
        return $this->print_expression_compiler->compile($attributes, $this, ['value' => $value, 'modifierlist' => $modifiers]);
    }
    /**
     * method to compile a Smarty template
     *
     * @param mixed $_content template source
     * @param bool $isTemplateSource
     *
     * @return bool true if compiling succeeded, false if it failed
     * @throws \Smarty\CompilerException
     */
    protected function do_compile($_content, $is_template_source = false): string
    {
        /* here is where the compiling takes place. Smarty
           tags in the templates are replaces with PHP code,
           then written to compiled files. */
        // init the lexer/parser to compile the template
        $this->parser = new Template_Parser(new Template_Lexer(str_replace(["\r\n", "\r"], "\n", $_content), $this), $this);
        if ($is_template_source && $this->template->caching) {
            $this->parser->insert_php_code("<?php\n\$_smarty_tpl->getCompiled()->nocache_hash = '{$this->nocache_hash}';\n?>\n");
        }
        if ($this->smarty->_parserdebug) {
            $this->parser->print_trace();
            $this->parser->lex->print_trace();
        }
        // get tokens from lexer and parse them
        while ($this->parser->lex->yylex()) {
            if ($this->smarty->_parserdebug) {
                echo "Line {$this->parser->lex->line} Parsing  {$this->parser->yy_token_name[$this->parser->lex->token]} Token " . $this->parser->lex->value;
            }
            $this->parser->do_parse($this->parser->lex->token, $this->parser->lex->value);
        }
        // finish parsing process
        $this->parser->do_parse(0, 0);
        // check for unclosed tags
        if ($this->get_tag_stack_count() > 0) {
            // get stacked info
            [$open_tag, $_data] = array_pop($this->_tag_stack);
            $this->trigger_template_error('unclosed ' . $this->smarty->get_left_delimiter() . $open_tag . $this->smarty->get_right_delimiter() . ' tag');
        }
        // call post compile callbacks
        foreach ($this->post_compile_callbacks as $cb) {
            $callback_function = $cb[0];
            $parameters = $cb;
            $parameters[0] = $this;
            $callback_function(...$parameters);
        }
        // return compiled code
        return $this->prefix_compiled_code . $this->parser->retvalue . $this->postfix_compiled_code;
    }
    /**
     * Register a post compile callback
     * - when the callback is called after template compiling the compiler object will be inserted as first parameter
     *
     * @param callback $callback
     * @param array $parameter optional parameter array
     * @param string $key optional key for callback
     * @param bool $replace if true replace existing keyed callback
     */
    public function register_post_compile_callback($callback, $parameter = [], $key = null, $replace = false): void
    {
        array_unshift($parameter, $callback);
        if (isset($key)) {
            if ($replace || !isset($this->post_compile_callbacks[$key])) {
                $this->post_compile_callbacks[$key] = $parameter;
            }
        } else {
            $this->post_compile_callbacks[] = $parameter;
        }
    }
    /**
     * Remove a post compile callback
     *
     * @param string $key callback key
     */
    public function unregister_post_compile_callback($key): void
    {
        unset($this->post_compile_callbacks[$key]);
    }
    /**
     * @throws Exception
     */
    private function can_compile_template_function_call(string $tag): bool
    {
        return isset($this->parent_compiler->tpl_function[$tag]) || $this->template->get_smarty()->has_runtime('TplFunction') && $this->template->get_smarty()->get_runtime('TplFunction')->get_tpl_function($this->template, $tag) !== false;
    }
    /**
     * @throws CompilerException
     */
    private function compile_registered_object_method_call(string $base_tag, array $args, array $parameter, string $tag)
    {
        $method = $parameter['object_method'];
        $allowed_as_block_function = in_array($method, $this->smarty->registered_objects[$base_tag][3]);
        if ($base_tag === $tag) {
            // opening tag
            $allowed_as_normal_function = empty($this->smarty->registered_objects[$base_tag][1]) || in_array($method, $this->smarty->registered_objects[$base_tag][1]);
            if ($allowed_as_block_function) {
                return $this->object_method_block_compiler->compile($args, $this, $parameter, $tag, $method);
            }
            if ($allowed_as_normal_function) {
                return $this->object_method_call_compiler->compile($args, $this, $parameter, $tag, $method);
            }
            $this->trigger_template_error('not allowed method "' . $method . '" in registered object "' . $tag . '"', null, true);
        }
        // closing tag
        if ($allowed_as_block_function) {
            return $this->object_method_block_compiler->compile($args, $this, $parameter, $tag, $method);
        }
        $this->trigger_template_error('not allowed closing tag method "' . $method . '" in registered object "' . $base_tag . '"', null, true);
    }
    public function compile_function_call(string $base_tag, array $args, array $parameter = []): string
    {
        return $this->function_call_compiler->compile($args, $this, $parameter, $base_tag, $base_tag);
    }
    public function compile_modifier_in_expression(string $function, array $_attr)
    {
        $value = array_shift($_attr);
        return $this->compile_modifier([array_merge([$function], $_attr)], $value);
    }
    public function get_parser(): ?Template_Parser
    {
        return $this->parser;
    }
    public function set_parser(?Template_Parser $parser): void
    {
        $this->parser = $parser;
    }
    /**
     * @return \Smarty\Template|null
     */
    public function get_template(): ?\Smarty\Template
    {
        return $this->template;
    }
    /**
     * @param \Smarty\Template|null $template
     */
    public function set_template(?\Smarty\Template $template): void
    {
        $this->template = $template;
    }
    public function get_parent_compiler(): ?Template
    {
        return $this->parent_compiler;
    }
    public function set_parent_compiler(?Template $parent_compiler): void
    {
        $this->parent_compiler = $parent_compiler;
    }
    /**
     * Push opening tag name on stack
     * Optionally additional data can be saved on stack
     *
     * @param string $openTag the opening tag's name
     * @param mixed $data optional data saved
     */
    public function open_tag($open_tag, $data = null): void
    {
        $this->_tag_stack[] = [$open_tag, $data];
        if ($open_tag == 'nocache') {
            $this->no_cache_stack_depth++;
        }
    }
    /**
     * Pop closing tag
     * Raise an error if this stack-top doesn't match with expected opening tags
     *
     * @param array|string $expectedTag the expected opening tag names
     *
     * @return mixed        any type the opening tag's name or saved data
     * @throws CompilerException
     */
    public function close_tag($expected_tag)
    {
        if ($this->get_tag_stack_count() > 0) {
            // get stacked info
            [$_open_tag, $_data] = array_pop($this->_tag_stack);
            // open tag must match with the expected ones
            if (in_array($_open_tag, (array) $expected_tag)) {
                if ($_open_tag == 'nocache') {
                    $this->no_cache_stack_depth--;
                }
                if (is_null($_data)) {
                    // return opening tag
                    return $_open_tag;
                }
                // return restored data
                return $_data;
            }
            // wrong nesting of tags
            $this->trigger_template_error("unclosed '" . $this->get_template()->get_left_delimiter() . "{$_open_tag}" . $this->get_template()->get_right_delimiter() . "' tag");
            return;
        }
        // wrong nesting of tags
        $this->trigger_template_error('unexpected closing tag', null, true);
    }
    /**
     * Returns true if we are in a {nocache}...{/nocache} block, but false if inside {block} tag inside a {nocache} block...
     */
    public function is_nocache_active(): bool
    {
        return !$this->suppress_nocache_processing && ($this->no_cache_stack_depth > 0 || $this->tag_nocache);
    }
    /**
     * Returns the full tag stack, used in the compiler for {break}
     */
    public function get_tag_stack(): array
    {
        return $this->_tag_stack;
    }
    /**
     * Should the next variable output be raw (true) or auto-escaped (false)
     */
    public function is_raw_output(): bool
    {
        return $this->raw_output;
    }
    /**
     * Should the next variable output be raw (true) or auto-escaped (false)
     */
    public function set_raw_output(bool $raw_output): void
    {
        $this->raw_output = $raw_output;
    }
}