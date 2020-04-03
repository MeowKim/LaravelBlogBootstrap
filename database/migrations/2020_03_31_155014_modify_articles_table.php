<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
            $table->bigInteger('updated_by')->nullable()->unsigned()->after('updated_at');
            $table->string('image_name', 255)->nullable()->after('content');
            $table->string('image', 255)->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('img');
            $table->dropColumn('img_name');
        });
    }
}
