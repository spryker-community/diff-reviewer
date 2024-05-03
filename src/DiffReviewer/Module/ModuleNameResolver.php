<?php

namespace DiffReviewer\DiffReviewer\Module;

class ModuleNameResolver
{
    public static function getModuleName(array $diff): string
    {
        $name = $diff['diff']->to();
        $pathParts = explode('/', $name);
        $moduleName = $pathParts[2];

        return $moduleName;
    }
}
