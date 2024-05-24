<?php

if(!function_exists('type_of')) {
    function type_of(string $key) : \Modules\TypesManager\Models\Type|null
    {
        $type = \Modules\TypesManager\Models\Type::where('key', $key)->first();
        return $type ?? null;
    }
}
