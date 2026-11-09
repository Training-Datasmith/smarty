<?php

declare (strict_types=1);
namespace Smarty\Extension;

class Core_Extension extends Base
{
    public function get_tag_compiler(string $tag): ?\Smarty\Compile\Compiler_Interface
    {
        switch ($tag) {
            case 'append':
                return new \Smarty\Compile\Tag\Append();
            case 'assign':
                return new \Smarty\Compile\Tag\Assign();
            case 'block':
                return new \Smarty\Compile\Tag\Block();
            case 'blockclose':
                return new \Smarty\Compile\Tag\Block_Close();
            case 'break':
                return new \Smarty\Compile\Tag\Break_Tag();
            case 'call':
                return new \Smarty\Compile\Tag\Call();
            case 'capture':
                return new \Smarty\Compile\Tag\Capture();
            case 'captureclose':
                return new \Smarty\Compile\Tag\Capture_Close();
            case 'config_load':
                return new \Smarty\Compile\Tag\Config_Load();
            case 'continue':
                return new \Smarty\Compile\Tag\Continue_Tag();
            case 'debug':
                return new \Smarty\Compile\Tag\Debug();
            case 'eval':
                return new \Smarty\Compile\Tag\Eval_Tag();
            case 'extends':
                return new \Smarty\Compile\Tag\Extends_Tag();
            case 'for':
                return new \Smarty\Compile\Tag\For_Tag();
            case 'foreach':
                return new \Smarty\Compile\Tag\Foreach_Tag();
            case 'foreachelse':
                return new \Smarty\Compile\Tag\Foreach_Else();
            case 'foreachclose':
                return new \Smarty\Compile\Tag\Foreach_Close();
            case 'forelse':
                return new \Smarty\Compile\Tag\For_Else();
            case 'forclose':
                return new \Smarty\Compile\Tag\For_Close();
            case 'function':
                return new \Smarty\Compile\Tag\Function_Tag();
            case 'functionclose':
                return new \Smarty\Compile\Tag\Function_Close();
            case 'if':
                return new \Smarty\Compile\Tag\If_Tag();
            case 'else':
                return new \Smarty\Compile\Tag\Else_Tag();
            case 'elseif':
                return new \Smarty\Compile\Tag\Else_If_Tag();
            case 'ifclose':
                return new \Smarty\Compile\Tag\If_Close();
            case 'include':
                return new \Smarty\Compile\Tag\Include_Tag();
            case 'ldelim':
                return new \Smarty\Compile\Tag\Ldelim();
            case 'rdelim':
                return new \Smarty\Compile\Tag\Rdelim();
            case 'nocache':
                return new \Smarty\Compile\Tag\Nocache();
            case 'nocacheclose':
                return new \Smarty\Compile\Tag\Nocache_Close();
            case 'section':
                return new \Smarty\Compile\Tag\Section();
            case 'sectionelse':
                return new \Smarty\Compile\Tag\Section_Else();
            case 'sectionclose':
                return new \Smarty\Compile\Tag\Section_Close();
            case 'setfilter':
                return new \Smarty\Compile\Tag\Setfilter();
            case 'setfilterclose':
                return new \Smarty\Compile\Tag\Setfilter_Close();
            case 'while':
                return new \Smarty\Compile\Tag\While_Tag();
            case 'whileclose':
                return new \Smarty\Compile\Tag\While_Close();
        }
        return null;
    }
}