<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Sectionelse Class
 *
 */
class Section_Else extends Base
{
    /**
     * Compiles code for the {sectionelse} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        [$open_tag, $nocache_pushed] = $this->close_tag($compiler, ['section']);
        $this->open_tag($compiler, 'sectionelse', ['sectionelse', $nocache_pushed]);
        return "<?php }} else {\n ?>";
    }
}