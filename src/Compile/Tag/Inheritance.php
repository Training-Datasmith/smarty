<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Shared Inheritance
 * Shared methods for {extends} and {block} tags
 *


 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Shared Inheritance Class
 *


 */
abstract class Inheritance extends Base
{
    /**
     * Compile inheritance initialization code as prefix
     *
     * @param bool|false                            $initChildSequence if true force child template
     */
    public static function postCompile(\Smarty\Compiler\Template $compiler, $initChildSequence = false): void
    {
        $compiler->prefixCompiledCode .= "<?php \$_smarty_tpl->getInheritance()->init(\$_smarty_tpl, " .
                                         var_export($initChildSequence, true) . ");\n?>\n";
    }

    /**
     * Register post compile callback to compile inheritance initialization code
     *
     * @param bool|false                            $initChildSequence if true force child template
     */
    public function registerInit(\Smarty\Compiler\Template $compiler, $initChildSequence = false): void
    {
        if ($initChildSequence || !isset($compiler->_cache[ 'inheritanceInit' ])) {
            $compiler->registerPostCompileCallback(
                [self::class, 'postCompile'],
                [$initChildSequence],
                'inheritanceInit',
                $initChildSequence
            );
            $compiler->_cache[ 'inheritanceInit' ] = true;
        }
    }
}
