<?php

declare(strict_types=1);
function default_script_function_tag($params, $template)
{
    return 'scriptfunction ' . $params['value'];
}
