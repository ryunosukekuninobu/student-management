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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // 保護者情報
            $table->string('name');
            $table->string('relationship'); // father, mother, grandfather, grandmother, other
            $table->string('phone');
            $table->string('email')->nullable();

            // 住所
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();

            // 連絡先優先度
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('can_pickup')->default(true); // お迎え可能か

            // メモ
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_profile_id']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
