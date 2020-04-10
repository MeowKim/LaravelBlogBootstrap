<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('id');
            $table->dropPrimary();
            $table->string('user_id', 20)->after('id')->primary();
            $table->string('image_name', 255)->nullable()->after('password');
            $table->string('image', 255)->nullable()->after('password');
            $table->boolean('is_admin')->after('password')->default(false);
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
            $table->dropPrimary();
            $table->primary('id');
            $table->dropUnique('users_unique_id');
            $table->dropColumn('user_id');
            $table->dropColumn('image_name');
            $table->dropColumn('image');
            $table->dropColumn('is_admin');
        });
    }
}
