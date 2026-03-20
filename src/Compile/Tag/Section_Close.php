<?php

declare (strict_types=1);
/**
 * Smarty Internal Plugin Compile Section
 * Compiles the {section} {sectionelse} {/section} tags
 *
 * @author     Uwe Tews
 */
namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
/**
 * Smarty Internal Plugin Compile Sectionclose Class
 */
class Section_Close extends Base
{
    /**
     * Compiles code for the {/section} tag
     *
     * @param array $args array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
    {
        $compiler->loop_nesting--;
        [$open_tag, $nocache_pushed] = $this->close_tag($compiler, ['section', 'sectionelse']);
        if ($nocache_pushed) {
            // pop the pushed virtual nocache tag
            $this->close_tag($compiler, 'nocache');
        }
        $output = "<?php\n";
        if ($open_tag === 'sectionelse') {
            $output .= "}\n";
        } else {
            $output .= "}\n}\n";
        }
        return $output . '?>';
    }
}