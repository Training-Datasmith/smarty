<?php

declare (strict_types=1);
namespace Smarty\Extension;

class Default_Extension extends Base
{
    private $modifiers = [];
    private $function_handlers = [];
    private $block_handlers = [];
    public function get_modifier_compiler(string $modifier): ?\Smarty\Compile\Modifier\Modifier_Compiler_Interface
    {
        if (isset($this->modifiers[$modifier])) {
            return $this->modifiers[$modifier];
        }
        switch ($modifier) {
            case 'cat':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Cat_Modifier_Compiler();
                break;
            case 'count_characters':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Count_Characters_Modifier_Compiler();
                break;
            case 'count_paragraphs':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Count_Paragraphs_Modifier_Compiler();
                break;
            case 'count_sentences':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Count_Sentences_Modifier_Compiler();
                break;
            case 'count_words':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Count_Words_Modifier_Compiler();
                break;
            case 'default':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Default_Modifier_Compiler();
                break;
            case 'empty':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Empty_Modifier_Compiler();
                break;
            case 'escape':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Escape_Modifier_Compiler();
                break;
            case 'from_charset':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\From_Charset_Modifier_Compiler();
                break;
            case 'indent':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Indent_Modifier_Compiler();
                break;
            case 'is_array':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Is_Array_Modifier_Compiler();
                break;
            case 'isset':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Isset_Modifier_Compiler();
                break;
            case 'json_encode':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Json_Encode_Modifier_Compiler();
                break;
            case 'lower':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Lower_Modifier_Compiler();
                break;
            case 'nl2br':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Nl2br_Modifier_Compiler();
                break;
            case 'noprint':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\No_Print_Modifier_Compiler();
                break;
            case 'raw':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Raw_Modifier_Compiler();
                break;
            case 'round':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Round_Modifier_Compiler();
                break;
            case 'str_repeat':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Str_Repeat_Modifier_Compiler();
                break;
            case 'string_format':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\String_Format_Modifier_Compiler();
                break;
            case 'strip':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Strip_Modifier_Compiler();
                break;
            case 'strip_tags':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Strip_Tags_Modifier_Compiler();
                break;
            case 'strlen':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Strlen_Modifier_Compiler();
                break;
            case 'substr':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Substr_Modifier_Compiler();
                break;
            case 'to_charset':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\To_Charset_Modifier_Compiler();
                break;
            case 'unescape':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Unescape_Modifier_Compiler();
                break;
            case 'upper':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Upper_Modifier_Compiler();
                break;
            case 'wordwrap':
                $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Word_Wrap_Modifier_Compiler();
                break;
        }
        return $this->modifiers[$modifier] ?? null;
    }
    public function get_modifier_callback(string $modifier_name): ?array
    {
        switch ($modifier_name) {
            case 'capitalize':
                return [$this, 'smarty_modifier_capitalize'];
            case 'count':
                return [$this, 'smarty_modifier_count'];
            case 'date_format':
                return [$this, 'smarty_modifier_date_format'];
            case 'debug_print_var':
                return [$this, 'smarty_modifier_debug_print_var'];
            case 'escape':
                return [$this, 'smarty_modifier_escape'];
            case 'explode':
                return [$this, 'smarty_modifier_explode'];
            case 'implode':
                return [$this, 'smarty_modifier_implode'];
            case 'in_array':
                return [$this, 'smarty_modifier_in_array'];
            case 'join':
                return [$this, 'smarty_modifier_join'];
            case 'mb_wordwrap':
                return [$this, 'smarty_modifier_mb_wordwrap'];
            case 'number_format':
                return [$this, 'smarty_modifier_number_format'];
            case 'regex_replace':
                return [$this, 'smarty_modifier_regex_replace'];
            case 'replace':
                return [$this, 'smarty_modifier_replace'];
            case 'spacify':
                return [$this, 'smarty_modifier_spacify'];
            case 'split':
                return [$this, 'smarty_modifier_split'];
            case 'truncate':
                return [$this, 'smarty_modifier_truncate'];
        }
        return null;
    }
    public function get_function_handler(string $function_name): ?\Smarty\Function_Handler\Function_Handler_Interface
    {
        if (isset($this->function_handlers[$function_name])) {
            return $this->function_handlers[$function_name];
        }
        switch ($function_name) {
            case 'count':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Count();
                break;
            case 'counter':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Counter();
                break;
            case 'cycle':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Cycle();
                break;
            case 'fetch':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Fetch();
                break;
            case 'html_checkboxes':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Html_Checkboxes();
                break;
            case 'html_image':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Html_Image();
                break;
            case 'html_options':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Html_Options();
                break;
            case 'html_radios':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Html_Radios();
                break;
            case 'html_select_date':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Html_Select_Date();
                break;
            case 'html_select_time':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Html_Select_Time();
                break;
            case 'html_table':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Html_Table();
                break;
            case 'mailto':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Mailto();
                break;
            case 'math':
                $this->function_handlers[$function_name] = new \Smarty\Function_Handler\Math();
                break;
        }
        return $this->function_handlers[$function_name] ?? null;
    }
    public function get_block_handler(string $block_tag_name): ?\Smarty\Block_Handler\Block_Handler_Interface
    {
        switch ($block_tag_name) {
            case 'textformat':
                $this->block_handlers[$block_tag_name] = new \Smarty\Block_Handler\Text_Format();
                break;
        }
        return $this->block_handlers[$block_tag_name] ?? null;
    }
    /**
     * Smarty spacify modifier plugin
     * Type:     modifier
     * Name:     spacify
     * Purpose:  add spaces between characters in a string
     *
     * @author Monte Ohrt <monte at ohrt dot com>
     *
     * @param string $string       input string
     * @param string $spacify_char string to insert between characters.
     */
    public function smarty_modifier_spacify($string, $spacify_char = ' '): string
    {
        // well… what about charsets besides latin and UTF-8?
        return implode($spacify_char, preg_split('//' . \Smarty\Smarty::$_UTF8_MODIFIER, $string, -1, PREG_SPLIT_NO_EMPTY));
    }
    /**
     * Smarty capitalize modifier plugin
     * Type:     modifier
     * Name:     capitalize
     * Purpose:  capitalize words in the string
     * {@internal {$string|capitalize:true:true} is the fastest option for MBString enabled systems }}
     *
     * @param string  $string    string to capitalize
     * @param boolean $uc_digits also capitalize "x123" to "X123"
     * @param boolean $lc_rest   capitalize first letters, lowercase all following letters "aAa" to "Aaa"
     *
     * @return string capitalized string
     * @author Monte Ohrt <monte at ohrt dot com>
     * @author Rodney Rehm
     */
    public function smarty_modifier_capitalize($string, $uc_digits = false, $lc_rest = false): ?string
    {
        $string = (string) $string;
        if ($lc_rest) {
            // uppercase (including hyphenated words)
            $upper_string = mb_convert_case($string, MB_CASE_TITLE, \Smarty\Smarty::$_CHARSET);
        } else {
            // uppercase word breaks
            $upper_string = preg_replace_callback("!(^|[^\\p{L}'])([\\p{Ll}])!S" . \Smarty\Smarty::$_UTF8_MODIFIER, function ($matches): string {
                return stripslashes($matches[1]) . mb_convert_case(stripslashes($matches[2]), MB_CASE_UPPER, \Smarty\Smarty::$_CHARSET);
            }, $string);
        }
        // check uc_digits case
        if (!$uc_digits) {
            if (preg_match_all("!\\b([\\p{L}]*[\\p{N}]+[\\p{L}]*)\\b!" . \Smarty\Smarty::$_UTF8_MODIFIER, $string, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as $match) {
                    $upper_string = substr_replace($upper_string, mb_strtolower($match[0], \Smarty\Smarty::$_CHARSET), $match[1], strlen($match[0]));
                }
            }
        }
        return preg_replace_callback("!((^|\\s)['\"])(\\w)!" . \Smarty\Smarty::$_UTF8_MODIFIER, function ($matches): string {
            return stripslashes($matches[1]) . mb_convert_case(stripslashes($matches[3]), MB_CASE_UPPER, \Smarty\Smarty::$_CHARSET);
        }, $upper_string);
    }
    /**
     * Smarty count modifier plugin
     * Type:     modifier
     * Name:     count
     * Purpose:  counts all elements in an array or in a Countable object
     * Input:
     *          - Countable|array: array or object to count
     *          - mode: int defaults to 0 for normal count mode, if set to 1 counts recursive
     *
     * @param mixed $arrayOrObject  input array/object
     * @param int $mode       count mode
     */
    public function smarty_modifier_count($array_or_object, $mode = 0): int
    {
        /*
         * @see https://www.php.net/count
         * > Prior to PHP 8.0.0, if the parameter was neither an array nor an object that implements the Countable interface,
         * > 1 would be returned, unless value was null, in which case 0 would be returned.
         */
        if ($array_or_object instanceof \Countable || is_array($array_or_object)) {
            return count($array_or_object, (int) $mode);
        }
        /*
         * @see https://www.php.net/count
         * > Prior to PHP 8.0.0, if the parameter was neither an array nor an object that implements the Countable interface,
         * > 1 would be returned, unless value was null, in which case 0 would be returned.
         */
        if ($array_or_object === null) {
            return 0;
        }
        return 1;
    }
    /**
     * Smarty date_format modifier plugin
     * Type:     modifier
     * Name:     date_format
     * Purpose:  format datestamps via strftime
     * Input:
     *          - string: input date string
     *          - format: strftime format for output
     *          - default_date: default date if $string is empty
     *
     * @author Monte Ohrt <monte at ohrt dot com>
     *
     * @param string $string       input date string
     * @param string $format       strftime format for output
     * @param string $default_date default date if $string is empty
     * @param string $formatter    either 'strftime' or 'auto'
     *
     * @return string |void
     * @uses   smarty_make_timestamp()
     */
    public function smarty_modifier_date_format($string, $format = null, $default_date = '', $formatter = 'auto')
    {
        if ($format === null) {
            $format = \Smarty\Smarty::$_DATE_FORMAT;
        }
        if (!empty($string) && $string !== '0000-00-00' && $string !== '0000-00-00 00:00:00') {
            $timestamp = smarty_make_timestamp($string);
        } elseif (!empty($default_date)) {
            $timestamp = smarty_make_timestamp($default_date);
        } else {
            return;
        }
        if ($formatter === 'strftime' || $formatter === 'auto' && strpos($format, '%') !== false) {
            if (\Smarty\Smarty::$_IS_WINDOWS) {
                $_win_from = ['%D', '%h', '%n', '%r', '%R', '%t', '%T'];
                $_win_to = ['%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S'];
                if (strpos($format, '%e') !== false) {
                    $_win_from[] = '%e';
                    $_win_to[] = sprintf('%\' 2d', date('j', $timestamp));
                }
                if (strpos($format, '%l') !== false) {
                    $_win_from[] = '%l';
                    $_win_to[] = sprintf('%\' 2d', date('h', $timestamp));
                }
                $format = str_replace($_win_from, $_win_to, $format);
            }
            // @ to suppress deprecation errors when running in PHP8.1 or higher.
            return @strftime($format, $timestamp);
        }
        return date($format, $timestamp);
    }
    /**
     * Smarty debug_print_var modifier plugin
     * Type:     modifier
     * Name:     debug_print_var
     * Purpose:  formats variable contents for display in the console
     *
     * @author Monte Ohrt <monte at ohrt dot com>
     *
     * @param array|object $var     variable to be formatted
     * @param int          $max     maximum recursion depth if $var is an array or object
     * @param int          $length  maximum string length if $var is a string
     * @param int          $depth   actual recursion depth
     * @param array        $objects processed objects in actual depth to prevent recursive object processing
     */
    public function smarty_modifier_debug_print_var($var, $max = 10, $length = 40, $depth = 0, $objects = []): string
    {
        $_replace = ["\n" => '\n', "\r" => '\r', "\t" => '\t'];
        switch (gettype($var)) {
            case 'array':
                $results = '<b>Array (' . count($var) . ')</b>';
                if ($depth === $max) {
                    break;
                }
                foreach ($var as $curr_key => $curr_val) {
                    $results .= '<br>' . str_repeat('&nbsp;', $depth * 2) . '<b>' . htmlspecialchars(strtr($curr_key, $_replace)) . '</b> =&gt; ' . $this->smarty_modifier_debug_print_var($curr_val, $max, $length, ++$depth, $objects);
                    $depth--;
                }
                break;
            case 'object':
                $object_vars = get_object_vars($var);
                $results = '<b>' . get_class($var) . ' Object (' . count($object_vars) . ')</b>';
                if (in_array($var, $objects)) {
                    $results .= ' called recursive';
                    break;
                }
                if ($depth === $max) {
                    break;
                }
                $objects[] = $var;
                foreach ($object_vars as $curr_key => $curr_val) {
                    $results .= '<br>' . str_repeat('&nbsp;', $depth * 2) . '<b> -&gt;' . htmlspecialchars(strtr($curr_key, $_replace)) . '</b> = ' . $this->smarty_modifier_debug_print_var($curr_val, $max, $length, ++$depth, $objects);
                    $depth--;
                }
                break;
            case 'boolean':
            case 'NULL':
            case 'resource':
                if (true === $var) {
                    $results = 'true';
                } elseif (false === $var) {
                    $results = 'false';
                } elseif (null === $var) {
                    $results = 'null';
                } else {
                    $results = htmlspecialchars((string) $var);
                }
                $results = '<i>' . $results . '</i>';
                break;
            case 'integer':
            case 'float':
                $results = htmlspecialchars((string) $var);
                break;
            case 'string':
                $results = strtr($var, $_replace);
                if (mb_strlen($var, \Smarty\Smarty::$_CHARSET) > $length) {
                    $results = mb_substr($var, 0, $length - 3, \Smarty\Smarty::$_CHARSET) . '...';
                }
                $results = htmlspecialchars('"' . $results . '"', ENT_QUOTES, \Smarty\Smarty::$_CHARSET);
                break;
            case 'unknown type':
            default:
                $results = strtr((string) $var, $_replace);
                if (mb_strlen($results, \Smarty\Smarty::$_CHARSET) > $length) {
                    $results = mb_substr($results, 0, $length - 3, \Smarty\Smarty::$_CHARSET) . '...';
                }
                $results = htmlspecialchars($results, ENT_QUOTES, \Smarty\Smarty::$_CHARSET);
        }
        return $results;
    }
    /**
     * Smarty escape modifier plugin
     * Type:     modifier
     * Name:     escape
     * Purpose:  escape string for output
     *
     * @author Monte Ohrt <monte at ohrt dot com>
     *
     * @param string  $string        input string
     * @param string  $esc_type      escape type
     * @param string  $char_set      character set, used for htmlspecialchars() or htmlentities()
     * @param boolean $double_encode encode already encoded entitites again, used for htmlspecialchars() or htmlentities()
     *
     * @return string escaped input string
     */
    public function smarty_modifier_escape($string, $esc_type = 'html', $char_set = null, $double_encode = true)
    {
        if (!$char_set) {
            $char_set = \Smarty\Smarty::$_CHARSET;
        }
        $string = (string) $string;
        switch ($esc_type) {
            case 'html':
                return htmlspecialchars($string, ENT_QUOTES, $char_set, $double_encode);
            case 'htmlall':
                $string = mb_convert_encoding($string, 'UTF-8', $char_set);
                return htmlentities($string, ENT_QUOTES, 'UTF-8', $double_encode);
            case 'url':
                return rawurlencode($string);
            case 'urlpathinfo':
                return str_replace('%2F', '/', rawurlencode($string));
            case 'quotes':
                // escape unescaped single quotes
                return preg_replace("%(?<!\\\\)'%", "\\'", $string);
            case 'hex':
                // escape every byte into hex
                // Note that the UTF-8 encoded character ä will be represented as %c3%a4
                $return = '';
                $_length = strlen($string);
                for ($x = 0; $x < $_length; $x++) {
                    $return .= '%' . bin2hex($string[$x]);
                }
                return $return;
            case 'hexentity':
                $return = '';
                foreach ($this->mb_to_unicode($string, \Smarty\Smarty::$_CHARSET) as $unicode) {
                    $return .= '&#x' . strtoupper(dechex($unicode)) . ';';
                }
                return $return;
            case 'decentity':
                $return = '';
                foreach ($this->mb_to_unicode($string, \Smarty\Smarty::$_CHARSET) as $unicode) {
                    $return .= '&#' . $unicode . ';';
                }
                return $return;
            case 'javascript':
                // escape quotes and backslashes, newlines, etc.
                return strtr($string, [
                    '\\' => '\\\\',
                    "'" => "\\'",
                    '"' => '\"',
                    "\r" => '\r',
                    "\n" => '\n',
                    '</' => '<\/',
                    // see https://html.spec.whatwg.org/multipage/scripting.html#restrictions-for-contents-of-script-elements
                    '<!--' => '<\!--',
                    '<s' => '<\s',
                    '<S' => '<\S',
                    '`' => '\\\\`',
                    '${' => '\\\\\\$\{',
                    "\x00" => '\x00',
                ]);
            case 'mail':
                return smarty_mb_str_replace(['@', '.'], [' [AT] ', ' [DOT] '], $string);
            case 'nonstd':
                // escape non-standard chars, such as ms document quotes
                $return = '';
                foreach ($this->mb_to_unicode($string, \Smarty\Smarty::$_CHARSET) as $unicode) {
                    if ($unicode >= 126) {
                        $return .= '&#' . $unicode . ';';
                    } else {
                        $return .= chr($unicode);
                    }
                }
                return $return;
            default:
                trigger_error("escape: unsupported type: {$esc_type} - returning unmodified string", E_USER_NOTICE);
                return $string;
        }
    }
    /**
     * convert characters to their decimal unicode equivalents
     *
     * @link   http://www.ibm.com/developerworks/library/os-php-unicode/index.html#listing3 for inspiration
     *
     * @param string $string   characters to calculate unicode of
     * @param string $encoding encoding of $string
     *
     * @return array sequence of unicodes
     * @author Rodney Rehm
     */
    private function mb_to_unicode(string $string, $encoding = null)
    {
        if ($encoding) {
            $expanded = mb_convert_encoding($string, 'UTF-32BE', $encoding);
        } else {
            $expanded = mb_convert_encoding($string, 'UTF-32BE');
        }
        return unpack('N*', $expanded);
    }
    /**
     * Smarty explode modifier plugin
     * Type:     modifier
     * Name:     explode
     * Purpose:  split a string by a string
     *
     * @param string   $separator
     * @param string   $string
     *
     */
    public function smarty_modifier_explode($separator, $string, ?int $limit = null): array
    {
        trigger_error('Using explode is deprecated. ' . 'Use split, using the array first, separator second.', E_USER_DEPRECATED);
        // provide $string default to prevent deprecation errors in PHP >=8.1
        return explode($separator, $string ?? '', $limit ?? PHP_INT_MAX);
    }
    /**
     * Smarty split modifier plugin
     * Type:     modifier
     * Name:     split
     * Purpose:  split a string by a string
     *
     * @param string $string
     * @param string   $separator
     *
     */
    public function smarty_modifier_split($string, $separator, ?int $limit = null): array
    {
        // provide $string default to prevent deprecation errors in PHP >=8.1
        return explode($separator, $string ?? '', $limit ?? PHP_INT_MAX);
    }
    /**
     * Smarty implode modifier plugin
     * Type:     modifier
     * Name:     implode
     * Purpose:  join an array of values into a single string
     *
     * @param array   $values
     * @param string   $separator
     */
    public function smarty_modifier_implode($values, $separator = ''): string
    {
        trigger_error('Using implode is deprecated. ' . 'Use join using the array first, separator second.', E_USER_DEPRECATED);
        if (is_array($separator)) {
            return implode((string) ($values ?? ''), $separator);
        }
        return implode((string) ($separator ?? ''), (array) $values);
    }
    /**
     * Smarty in_array modifier plugin
     * Type:     modifier
     * Name:     in_array
     * Purpose:  test if value is contained in an array
     *
     * @param mixed   $needle
     * @param array   $array
     * @param bool   $strict
     */
    public function smarty_modifier_in_array($needle, $array, $strict = false): bool
    {
        return in_array($needle, (array) $array, (bool) $strict);
    }
    /**
     * Smarty join modifier plugin
     * Type:     modifier
     * Name:     join
     * Purpose:  join an array of values into a single string
     *
     * @param array   $values
     * @param string   $separator
     */
    public function smarty_modifier_join($values, $separator = ''): string
    {
        if (is_array($separator)) {
            trigger_error('Using join with the separator first is deprecated. ' . 'Call join using the array first, separator second.', E_USER_DEPRECATED);
            return implode((string) ($values ?? ''), $separator);
        }
        return implode((string) ($separator ?? ''), (array) $values);
    }
    /**
     * Smarty wordwrap modifier plugin
     * Type:     modifier
     * Name:     mb_wordwrap
     * Purpose:  Wrap a string to a given number of characters
     *
     * @link   https://php.net/manual/en/function.wordwrap.php for similarity
     *
     * @param string  $str   the string to wrap
     * @param int     $width the width of the output
     * @param string  $break the character used to break the line
     * @param boolean $cut   ignored parameter, just for the sake of
     *
     * @return string  wrapped string
     * @author Rodney Rehm
     */
    public function smarty_modifier_mb_wordwrap($str, $width = 75, $break = "\n", $cut = false)
    {
        return smarty_mb_wordwrap($str, $width, $break, $cut);
    }
    /**
     * Smarty number_format modifier plugin
     * Type:     modifier
     * Name:     number_format
     * Purpose:  Format a number with grouped thousands
     *
     *
     */
    public function smarty_modifier_number_format(?float $num, int $decimals = 0, ?string $decimal_separator = '.', ?string $thousands_separator = ','): string
    {
        // provide $num default to prevent deprecation errors in PHP >=8.1
        return number_format($num ?? 0.0, $decimals, $decimal_separator, $thousands_separator);
    }
    /**
     * Smarty regex_replace modifier plugin
     * Type:     modifier
     * Name:     regex_replace
     * Purpose:  regular expression search/replace
     *
     * @author Monte Ohrt <monte at ohrt dot com>
     *
     * @param string       $string  input string
     * @param string|array $search  regular expression(s) to search for
     * @param string|array $replace string(s) that should be replaced
     * @param int          $limit   the maximum number of replacements
     *
     * @return string
     */
    public function smarty_modifier_regex_replace($string, $search, $replace, $limit = -1): ?string
    {
        if (is_array($search)) {
            foreach ($search as $idx => $s) {
                $search[$idx] = $this->regex_replace_check($s);
            }
        } else {
            $search = $this->regex_replace_check($search);
        }
        return preg_replace($search, $replace, $string, $limit);
    }
    /**
     * @param  string $search string(s) that should be replaced
     *
     * @return string
     * @ignore
     */
    private function regex_replace_check($search)
    {
        // null-byte injection detection
        // anything behind the first null-byte is ignored
        if (($pos = strpos($search, "\x00")) !== false) {
            $search = substr($search, 0, $pos);
        }
        // reject patterns containing eval-modifier
        if (preg_match('!([a-zA-Z\s]+)$!s', $search, $match) && strpos($match[1], 'e') !== false) {
            trigger_error('regex_replace: the /e modifier is not allowed', E_USER_WARNING);
            return false;
        }
        return $search;
    }
    /**
     * Smarty replace modifier plugin
     * Type:     modifier
     * Name:     replace
     * Purpose:  simple search/replace
     *
     * @author Monte Ohrt <monte at ohrt dot com>
     * @author Uwe Tews
     *
     * @param string $string  input string
     * @param string $search  text to search for
     * @param string $replace replacement text
     *
     * @return string
     */
    public function smarty_modifier_replace($string, $search, $replace)
    {
        return smarty_mb_str_replace($search, $replace, $string);
    }
    /**
     * Smarty truncate modifier plugin
     * Type:     modifier
     * Name:     truncate
     * Purpose:  Truncate a string to a certain length if necessary,
     *               optionally splitting in the middle of a word, and
     *               appending the $etc string or inserting $etc into the middle.
     *
     * @author Monte Ohrt <monte at ohrt dot com>
     *
     * @param string  $string      input string
     * @param integer $length      length of truncated text
     * @param string  $etc         end string
     * @param boolean $break_words truncate at word boundary
     * @param boolean $middle      truncate in the middle of text
     *
     * @return string truncated string
     */
    public function smarty_modifier_truncate($string, $length = 80, string $etc = '...', $break_words = false, $middle = false)
    {
        if ($length === 0 || $string === null) {
            return '';
        }
        if (mb_strlen($string, \Smarty\Smarty::$_CHARSET) > $length) {
            $length -= min($length, mb_strlen($etc, \Smarty\Smarty::$_CHARSET));
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/' . \Smarty\Smarty::$_UTF8_MODIFIER, '', mb_substr($string, 0, $length + 1, \Smarty\Smarty::$_CHARSET));
            }
            if (!$middle) {
                return mb_substr($string, 0, $length, \Smarty\Smarty::$_CHARSET) . $etc;
            }
            return mb_substr($string, 0, intval($length / 2), \Smarty\Smarty::$_CHARSET) . $etc . mb_substr($string, -intval($length / 2), $length, \Smarty\Smarty::$_CHARSET);
        }
        return $string;
    }
}