<?php

declare(strict_types=1);
if (!class_exists('DefModifier')) {
    class DefModifier
    {
        public static function default_static_modifier($input)
        {
            return 'staticmodifier ' . $input;
        }
    }
}
