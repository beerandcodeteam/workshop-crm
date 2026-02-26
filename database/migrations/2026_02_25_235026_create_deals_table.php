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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('pipeline_stage_id')->constrained()->restrictOnDelete();
            $table->string('title', 255);
            $table->decimal('value', 12, 2)->default(0);
            $table->text('loss_reason')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('lead_id');
            $table->index('user_id');
            $table->index('pipeline_stage_id');
            $table->index(['tenant_id', 'pipeline_stage_id', 'sort_order'], 'deals_kanban_order_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
