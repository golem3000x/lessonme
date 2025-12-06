<?php

declare(strict_types=1);

use App\Enums\System\InvitationStatusEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableNameEnum::TEACHER_INVITATIONS->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')
                ->constrained(TableNameEnum::USERS->value)
                ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained(TableNameEnum::USERS->value)
                ->cascadeOnDelete();
            $table->foreignId('message_id')
                ->nullable()
                ->constrained(TableNameEnum::USER_MESSAGES->value)
                ->nullOnDelete();
            $table->string('status', 20)->default(InvitationStatusEnum::PENDING->value);
            $table->timestamps();

            $table->index('teacher_id');
            $table->index('student_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::TEACHER_INVITATIONS->value);
    }
};
