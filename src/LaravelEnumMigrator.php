<?php

namespace Liquid207\LaravelEnumMigration;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class LaravelEnumMigrator
{
    /**
     * Get enum column options.
     *
     * @param string $table_name
     * @param string $column_name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public static function getOptionsByColumnName($table_name, $column_name)
    {
        $column_definition = static::getColumnDefinition($table_name, $column_name);

        $type = $column_definition['Type'];

        if (!Str::startsWith($type, 'enum')) {
            throw new InvalidArgumentException('Only support enum column.');
        }

        preg_match("/^enum\\(\\'(.*)\\'\\)$/", $type, $matches);
        $options = explode("','", $matches[1]);

        return $options;
    }

    /**
     * Modify enum column use given options.
     *
     * @param string $table_name
     * @param string $column_name
     * @param array  $options
     *
     * @return bool
     */
    public static function modifyWithOptions($table_name, $column_name, $options)
    {
        $column_definition = static::getColumnDefinition($table_name, $column_name);

        $sql = "alter table `$table_name` modify `$column_name` enum( ";

        $options_str = implode(',', array_map(function ($option) {
            return "'$option'";
        }, $options));

        $sql .= $options_str;
        $sql .= ' )';

        if ($column_definition['Collation'] !== null) {
            $sql .= ' collate ' . $column_definition['Collation'];
        }

        if ($column_definition['Null'] === 'NO') {
            $sql .= ' not null';
        } else {
            if ($column_definition['Default'] === null) {
                $sql .= ' default null';
            }
        }

        if ($column_definition['Default']) {
            $sql .= ' default ' . $column_definition['Default'];
        }

        return DB::statement($sql);
    }

    /**
     * @param string $table_name
     * @param string $column_name
     *
     * @return array
     */
    private static function getColumnDefinition($table_name, $column_name)
    {
        $column_definition = DB::selectOne("show full columns from $table_name where `field` = '$column_name'");

        if ($column_definition === null) {
            throw new InvalidArgumentException("The Column [$column_name] not exist in table [$table_name].");
        }

        return $column_definition;
    }
}
