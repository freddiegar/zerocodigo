<?php

namespace FlexPHP\Generator\Domain\Builders\Entity;

use FlexPHP\Generator\Domain\Constants\Keyword;

trait TypeHintTrait
{
    protected function guessTypeHint(array $properties): string
    {
        $typeHint = 'string';
        $dataType = $properties[Keyword::DATA_TYPE] ?? 'string';

        switch ($dataType) {
            case 'smallint':
            case 'integer':
            case 'bigint':
            case 'decimal':
                $typeHint = 'int';
                break;
            case 'float':
                $typeHint = 'float';
                break;
            case 'bool':
            case 'boolean':
                $typeHint = 'bool';
                break;
            case 'array':
            case 'simple_array':
            case 'json_array':
                $typeHint = 'array';
                break;
            // Only 7.2
            // case 'object':
                // $typeHint = 'object';
                // break;
        }

        return $typeHint;
    }
}
