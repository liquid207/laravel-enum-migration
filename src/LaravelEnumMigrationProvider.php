<?php

namespace Liquid207\LaravelEnumMigration;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class LaravelEnumMigrationProvider extends ServiceProvider
{
    public function boot()
    {
        /**
         * @param string       $column_name
         * @param string|array $new_options
         *
         * @return bool
         */
        Blueprint::macro('enumAppend', function ($column_name, $new_options) {
            $options = LaravelEnumMigrator::getOptionsByColumnName($this->table, $column_name);

            if (!is_array($new_options)) {
                $new_options = [$new_options];
            }

            foreach ($new_options as $new_option) {
                if (!in_array($new_option, $options, true)) {
                    array_push($options, $new_option);
                }
            }

            return LaravelEnumMigrator::modifyWithOptions($this->table, $column_name, $options);
        });

        /**
         * @param string       $column_name
         * @param string|array $need_remove_options
         *
         * @return bool
         */
        Blueprint::macro('enumRemove', function ($column_name, $need_remove_options) {
            $options = LaravelEnumMigrator::getOptionsByColumnName($this->table, $column_name);

            if (!is_array($need_remove_options)) {
                $need_remove_options = [$need_remove_options];
            }

            $options = array_diff($options, $need_remove_options);

            return LaravelEnumMigrator::modifyWithOptions($this->table, $column_name, $options);
        });
    }
}
