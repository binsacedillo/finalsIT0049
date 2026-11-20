<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_file_id', 100)->nullable()->after('image');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_file_id', 100)->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_file_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image_file_id');
        });
    }
};
