<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Config File Compiler
 * This is the config file compiler class. It calls the lexer and parser to
 * perform the compiling.
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compiler;

use Smarty\Compiler_Exception;
use Smarty\Lexer\Configfile_Lexer;
use Smarty\Parser\Configfile_Parser;
use Smarty\Smarty;
use Smarty\Template;
/**
 * Main config file compiler class
 *
 */
class Configfile extends Base_Compiler
{
    /**
     * Lexer object
     *
     * @var ConfigfileLexer
     */
    public $lex;
    /**
     * Parser object
     *
     * @var ConfigfileParser
     */
    public $parser;
    /**
     * Smarty object
     *
     * @var Smarty object
     */
    public $smarty;
    /**
     * Smarty object
     *
     * @var Template object
     */
    public $template;
    /**
     * Compiled config data sections and variables
     *
     * @var array
     */
    public $config_data = [];
    /**
     * Initialize compiler
     *
     * @param Smarty $smarty global instance
     */
    public function __construct(Smarty $smarty)
    {
        $this->smarty = $smarty;
        $this->config_data['sections'] = [];
        $this->config_data['vars'] = [];
    }
    /**
     * Method to compile Smarty config source.
     *
     *
     * @return bool true if compiling succeeded, false if it failed
     * @throws \Smarty\Exception
     */
    public function compile_template(Template $template): string
    {
        $this->template = $template;
        $this->template->get_compiled()->file_dependency[$this->template->get_source()->uid] = [$this->template->get_source()->get_resource_name(), $this->template->get_source()->get_time_stamp(), $this->template->get_source()->type];
        if ($this->smarty->debugging) {
            $this->smarty->get_debug()->start_compile($this->template);
        }
        // init the lexer/parser to compile the config file
        /* @var ConfigfileLexer $this->lex */
        $this->lex = new Configfile_Lexer(str_replace(["\r\n", "\r"], "\n", $template->get_source()->get_content()) . "\n", $this);
        $this->parser = new Configfile_Parser($this->lex, $this);
        if ($this->smarty->_parserdebug) {
            $this->parser->print_trace();
        }
        // get tokens from lexer and parse them
        while ($this->lex->yylex()) {
            if ($this->smarty->_parserdebug) {
                echo "Parsing  {$this->parser->yy_token_name[$this->lex->token]} Token {$this->lex->value} Line {$this->lex->line} \n";
            }
            $this->parser->do_parse($this->lex->token, $this->lex->value);
        }
        // finish parsing process
        $this->parser->do_parse(0, 0);
        if ($this->smarty->debugging) {
            $this->smarty->get_debug()->end_compile($this->template);
        }
        // template header code
        $template_header = sprintf("<?php /* Smarty version %s, created on %s\n         compiled from '%s' */ ?>\n", \Smarty\Smarty::SMARTY_VERSION, date('Y-m-d H:i:s'), str_replace('*/', '* /', $this->template->get_source()->get_full_resource_name()));
        $code = '<?php $_smarty_tpl->parent->assignConfigVars(' . var_export($this->config_data, true) . ', $_smarty_tpl->getValue("sections")); ?>';
        return $template_header . $this->template->create_code_frame($code);
    }
    /**
     * display compiler error messages without dying
     * If parameter $args is empty it is a parser detected syntax error.
     * In this case the parser is called to obtain information about expected tokens.
     * If parameter $args contains a string this is used as error message
     *
     * @param string $args individual error message or null
     *
     * @throws CompilerException
     */
    public function trigger_config_file_error($args = null): void
    {
        // get config source line which has error
        $line = $this->lex->line;
        if (isset($args)) {
            // $line--;
        }
        $match = preg_split("/\n/", $this->lex->data);
        $error_text = "Syntax error in config file '{$this->template->get_source()->get_full_resource_name()}' on line {$line} '{$match[$line - 1]}' ";
        if (isset($args)) {
            // individual error message
            $error_text .= $args;
        } else {
            // expected token from parser
            foreach ($this->parser->yy_get_expected_tokens($this->parser->yymajor) as $token) {
                $exp_token = $this->parser->yy_token_name[$token];
                if (isset($this->lex->smarty_token_names[$exp_token])) {
                    // token type from lexer
                    $expect[] = '"' . $this->lex->smarty_token_names[$exp_token] . '"';
                } else {
                    // otherwise internal token name
                    $expect[] = $this->parser->yy_token_name[$token];
                }
            }
            // output parser error message
            $error_text .= ' - Unexpected "' . $this->lex->value . '", expected one of: ' . implode(' , ', $expect);
        }
        throw new Compiler_Exception($error_text);
    }
}