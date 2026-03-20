<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Foreachelse Class
 *
 */
class Foreach_Else extends Base
{
    /**
     * Compiles code for the {foreachelse} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        [$open_tag, $nocache_pushed, $local_variable_prefix, $item, $restore] = $this->close_tag($compiler, ['foreach']);
        $this->open_tag($compiler, 'foreachelse', ['foreachelse', $nocache_pushed, $local_variable_prefix, $item, false]);
        $output = "<?php\n";
        if ($restore) {
            $output .= "\$_smarty_tpl->setVariable('{$item}', {$local_variable_prefix}Backup);\n";
        }
        return $output . "}\nif ({$local_variable_prefix}DoElse) {\n?>";
    }
}