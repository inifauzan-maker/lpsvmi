<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->json('data');
            $table->timestamps();
        });

        $this->backfillFromRecords('data-leads', 'leads');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }

    private function backfillFromRecords(string $module, string $table): void
    {
        if (!Schema::hasTable('records')) {
            return;
        }

        $records = DB::table('records')->where('module', $module)->get();
        foreach ($records as $record) {
            DB::table($table)->insert([
                'data' => $record->data,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }
    }
};
