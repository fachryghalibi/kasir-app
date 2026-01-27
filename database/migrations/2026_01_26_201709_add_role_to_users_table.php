<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom role (enum: boss, employee)
            $table->enum('role', ['boss', 'employee'])
                ->default('employee')
                ->after('email');
            
            // Status aktif/nonaktif
            $table->boolean('is_active')
                ->default(true)
                ->after('role');
            
            // Tambah UUID untuk public ID
            $table->uuid('uuid')
                ->unique()
                ->after('id');
            
            // Phone number
            $table->string('phone', 20)
                ->nullable()
                ->after('email');
            
            // Soft deletes
            $table->softDeletes();
            
            // Index untuk performa
            $table->index('role');
            $table->index('is_active');
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'is_active',
                'uuid',
                'phone'
            ]);
            $table->dropSoftDeletes();
        });
    }
};