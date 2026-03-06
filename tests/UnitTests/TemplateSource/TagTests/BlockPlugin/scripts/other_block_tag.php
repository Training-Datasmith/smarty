<?php

declare(strict_types=1);
function default_block_tag($params, $content, $template, &$repeat)
{
    if (isset($content)) {
        return 'defaultblock ' . $content;
    }
}
