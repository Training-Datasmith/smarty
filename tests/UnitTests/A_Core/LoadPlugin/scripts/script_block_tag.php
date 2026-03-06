<?php

declare(strict_types=1);
function default_script_block_tag($params, $content, $template, &$repeat)
{
    if (isset($content)) {
        return 'scriptblock ' . $content;
    }
}
