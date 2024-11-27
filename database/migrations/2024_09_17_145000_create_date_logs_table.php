<?php

use App\Models\Borrow;
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
        Schema::create('date_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Borrow::class)->constrained()->cascadeOnDelete();
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('date_logs');
    }
};
