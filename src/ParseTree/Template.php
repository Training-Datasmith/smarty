<?php

namespace Smarty\ParseTree;

/**
 * Smarty Internal Plugin Templateparser Parse Tree
 * These are classes to build parse tree in the template parser
 *


 * @author     Thue Kristensen
 * @author     Uwe Tews
 */

/**
 * Template element
 *


 * @ignore
 */
class Template extends Base
{
    /**
     * Array of template elements
     *
     * @var array
     */
    public $subtrees = [];

    /**
     * Append buffer to subtree
     */
    public function append_subtree(\Smarty\Parser\TemplateParser $parser, Base $subtree): void
    {
        if (!empty($subtree->subtrees)) {
            $this->subtrees = array_merge($this->subtrees, $subtree->subtrees);
        } else {
            if ($subtree->data !== '') {
                $this->subtrees[] = $subtree;
            }
        }
    }

    /**
     * Append array to subtree
     *
     * @param Base[] $array
     */
    public function append_array(\Smarty\Parser\TemplateParser $parser, $array = []): void
    {
        if (!empty($array)) {
            $this->subtrees = array_merge($this->subtrees, (array)$array);
        }
    }

    /**
     * Prepend array to subtree
     *
     * @param Base[] $array
     */
    public function prepend_array(\Smarty\Parser\TemplateParser $parser, $array = []): void
    {
        if (!empty($array)) {
            $this->subtrees = array_merge((array)$array, $this->subtrees);
        }
    }

    /**
     * Sanitize and merge subtree buffers together
     *
     *
     * @return string template code content
     */
    public function to_smarty_php(\Smarty\Parser\TemplateParser $parser)
    {
        $code = '';

        foreach ($this->getChunkedSubtrees() as $chunk) {
            $text = '';
            switch ($chunk['mode']) {
                case 'textstripped':
                    foreach ($chunk['subtrees'] as $subtree) {
                        $text .= $subtree->to_smarty_php($parser);
                    }
                    $code .= preg_replace(
                        '/((<%)|(%>)|(<\?php)|(<\?)|(\?>)|(<\/?script))/',
                        "<?php echo '\$1'; ?>\n",
                        $parser->compiler->processText($text)
                    );
                    break;
                case 'text':
                    foreach ($chunk['subtrees'] as $subtree) {
                        $text .= $subtree->to_smarty_php($parser);
                    }
                    $code .= preg_replace(
                        '/((<%)|(%>)|(<\?php)|(<\?)|(\?>)|(<\/?script))/',
                        "<?php echo '\$1'; ?>\n",
                        $text
                    );
                    break;
                case 'tag':
                    foreach ($chunk['subtrees'] as $subtree) {
                        $text = $parser->compiler->appendCode($text, (string) $subtree->to_smarty_php($parser));
                    }
                    $code .= $text;
                    break;
                default:
                    foreach ($chunk['subtrees'] as $subtree) {
                        $text = $subtree->to_smarty_php($parser);
                    }
                    $code .= $text;

            }
        }
        return $code;
    }

    /**
     * @return array{mode: ('other' | 'tag' | 'text' | 'textstripped' | null), subtrees: list}[]
     */
    private function getChunkedSubtrees(): array {
        $chunks = [];
        $currentMode = null;
        $currentChunk = [];
        for ($key = 0, $cnt = count($this->subtrees); $key < $cnt; $key++) {

            if ($this->subtrees[ $key ]->data === '' && in_array($currentMode, ['textstripped', 'text', 'tag'])) {
                continue;
            }

            if ($this->subtrees[ $key ] instanceof Text
                && $this->subtrees[ $key ]->isToBeStripped()) {
                $newMode = 'textstripped';
            } elseif ($this->subtrees[ $key ] instanceof Text) {
                $newMode = 'text';
            } elseif ($this->subtrees[ $key ] instanceof Tag) {
                $newMode = 'tag';
            } else {
                $newMode = 'other';
            }

            if ($newMode == $currentMode) {
                $currentChunk[] = $this->subtrees[ $key ];
            } else {
                $chunks[] = [
                    'mode' => $currentMode,
                    'subtrees' => $currentChunk
                ];
                $currentMode = $newMode;
                $currentChunk = [$this->subtrees[ $key ]];
            }
        }
        if ($currentMode && $currentChunk) {
            $chunks[] = [
                'mode' => $currentMode,
                'subtrees' => $currentChunk
            ];
        }
        return $chunks;
    }
}
