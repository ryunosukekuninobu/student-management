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
        Schema::create('student_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');

            // 招待情報
            $table->string('email');
            $table->string('name');
            $table->string('user_type')->default('student'); // student, guardian
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, expired

            // オプション情報
            $table->json('metadata')->nullable(); // 追加情報（クラスID、学年など）

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_invitations');
    }
};
