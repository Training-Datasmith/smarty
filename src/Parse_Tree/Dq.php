<?php

declare (strict_types=1);
namespace Smarty\Parse_Tree;

/**
 * Double-quoted string inside a tag.
 *
 * @ignore
 */
/**
 * Double quoted string inside a tag.
 *
 * @ignore
 */
class Dq extends Base
{
    /**
     * Create parse tree buffer for double-quoted string subtrees
     *
     * @param object                    $parser  parser object
     * @param Base $subtree parse tree buffer
     */
    public function __construct($parser, Base $subtree)
    {
        $this->subtrees[] = $subtree;
        if ($subtree instanceof Tag) {
            $parser->block_nesting_level = $parser->compiler->get_tag_stack_count();
        }
    }
    /**
     * Append buffer to subtree
     *
     * @param Base $subtree parse tree buffer
     */
    public function append_subtree(\Smarty\Parser\Template_Parser $parser, Base $subtree): void
    {
        $last_subtree = count($this->subtrees) - 1;
        if ($last_subtree >= 0 && $this->subtrees[$last_subtree] instanceof Tag && $this->subtrees[$last_subtree]->saved_block_nesting < $parser->block_nesting_level) {
            if ($subtree instanceof Code) {
                $this->subtrees[$last_subtree]->data = $parser->compiler->append_code((string) $this->subtrees[$last_subtree]->data, '<?php echo ' . $subtree->data . ';?>');
            } elseif ($subtree instanceof Dq_Content) {
                $this->subtrees[$last_subtree]->data = $parser->compiler->append_code((string) $this->subtrees[$last_subtree]->data, '<?php echo "' . $subtree->data . '";?>');
            } else {
                $this->subtrees[$last_subtree]->data = $parser->compiler->append_code((string) $this->subtrees[$last_subtree]->data, (string) $subtree->data);
            }
        } else {
            $this->subtrees[] = $subtree;
        }
        if ($subtree instanceof Tag) {
            $parser->block_nesting_level = $parser->compiler->get_tag_stack_count();
        }
    }
    /**
     * Merge subtree buffer content together
     *
     *
     * @return string compiled template code
     */
    public function to_smarty_php(\Smarty\Parser\Template_Parser $parser): string
    {
        $code = '';
        foreach ($this->subtrees as $subtree) {
            if ($code !== '') {
                $code .= '.';
            }
            if ($subtree instanceof Tag) {
                $more_php = $subtree->assign_to_var($parser);
            } else {
                $more_php = $subtree->to_smarty_php($parser);
            }
            $code .= $more_php;
            if (!$subtree instanceof Dq_Content) {
                $parser->compiler->has_variable_string = true;
            }
        }
        return $code;
    }
}