<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default(UserRole::User->value)->after('password');
            $table->string('status')->default(UserStatus::Pending->value)->after('role');
            $table->foreignId('position_id')->nullable()->after('status')->constrained()->nullOnDelete();
            $table->foreignId('work_unit_id')->nullable()->after('position_id')->constrained()->nullOnDelete();
            $table->string('key_pass_hash')->nullable()->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('key_pass_hash');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'role',
                'status',
                'key_pass_hash',
                'last_login_at',
            ]);
            $table->dropConstrainedForeignId('position_id');
            $table->dropConstrainedForeignId('work_unit_id');
        });
    }
};
