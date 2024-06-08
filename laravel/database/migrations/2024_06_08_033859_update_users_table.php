<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename 'name' column to 'username'
            $table->renameColumn('name', 'username');
            
            // Add new columns
            $table->string('first_name')->after('name');
            $table->string('last_name')->after('first_name');
            $table->string('mobile')->nullable()->after('last_name');
            $table->string('profile_image')->nullable()->after('mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
