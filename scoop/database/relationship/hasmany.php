<?php

namespace Scoop\Database\Relationship;

use Scoop\Database\Model;

/**
 * Class HasMany
 * @package Scoop\Database\Relationship
 */
class HasMany {

    /**
     * @param       $parentClass
     * @param       $childClass
     * @param array $properties
     *
     * @return string
     */
    public static function get_sql ( $parentClass, $childClass, array $properties ) {

        return 'LEFT JOIN ' .
            $childClass::get_sql_table_name( true ) .
            ' ON ( ' .
                $parentClass::get_sql_table_name_alias() . ".`{$properties['parentColumn']}`" .
                ' = ' .
                $childClass::get_sql_table_name_alias() . ".`{$properties['childColumn']}`".
            ' )';
    }
}
