<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCompaniesToGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::beginTransaction();

        Schema::table('admin_user_companies', function (Blueprint $table) {
            $table->renameColumn('company_id', 'group_id');
        });

        Schema::table('company_ports', function (Blueprint $table) {
            $table->renameColumn('company_id', 'group_id');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->renameColumn('company_id', 'group_id');
        });

        Schema::rename('companies', 'groups');
        Schema::rename('admin_user_companies', 'admin_user_groups');
        Schema::rename('company_ports', 'group_ports');

        \DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::beginTransaction();

        Schema::table('admin_user_groups', function (Blueprint $table) {
            $table->renameColumn('group_id', 'company_id');
        });

        Schema::table('group_ports', function (Blueprint $table) {
            $table->renameColumn('group_id', 'company_id');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->renameColumn('group_id', 'company_id');
        });

        Schema::rename('groups', 'companies');
        Schema::rename('admin_user_groups', 'admin_user_companies');
        Schema::rename('group_ports', 'company_ports');

        \DB::commit();
    }
}
