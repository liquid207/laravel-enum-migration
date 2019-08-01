# laravel-enum-migration
Macros for migrate enum column.

# Features

* Append a new option to enum column.
* Remove options from enum column by name.

# Installation

* Run `composer require liquid207/laravel-enum-migration`

# Usage

```php
class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Append one
            $table->enumAppend('type', 'option1');
            
            // Append array
            $table->enumAppend('type', ['option2', 'option3']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove one
            $table->enumRemove('type', 'option1');
            
            // Remove array
            $table->enumRemove('type', ['option2', 'option3']);
        });
    }
}

```
