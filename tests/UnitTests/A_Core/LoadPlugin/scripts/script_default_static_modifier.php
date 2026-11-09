<?php

declare(strict_types=1);
if (!class_exists(Def_Modifier::class)) {
    class Def_Modifier
    {
        public static function default_static_modifier($input)
        {
            return 'staticmodifier ' . $input;
        }
    }
}
