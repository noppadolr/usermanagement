<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_units', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['work_group_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_units');
    }
};
