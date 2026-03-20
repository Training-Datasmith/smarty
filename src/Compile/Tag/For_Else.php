<?php

declare (strict_types=1);
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Forelse Class
 *
 */
class For_Else extends Base
{
    /**
     * Compiles code for the {forelse} tag
     *
     * @param array $args array with attributes from parser
     * @param object $compiler compiler object
     * @param array $parameter array with compilation parameter
     *
     * @return string compiled code
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        [$tag_name, $nocache_pushed] = $this->close_tag($compiler, ['for']);
        $this->open_tag($compiler, 'forelse', ['forelse', $nocache_pushed]);
        return '<?php }} else { ?>';
    }
}