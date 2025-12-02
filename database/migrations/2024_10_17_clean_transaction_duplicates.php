<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "Checking for duplicate hashes in transactions table...\n";
        
        // Находим дубликаты
        $duplicates = DB::select("
            SELECT hash, COUNT(*) as count 
            FROM transactions 
            WHERE hash IS NOT NULL 
            GROUP BY hash 
            HAVING COUNT(*) > 1
            LIMIT 10
        ");
        
        if (empty($duplicates)) {
            echo "No duplicates found.\n";
            return;
        }
        
        echo "Found " . count($duplicates) . " duplicate hashes:\n";
        foreach ($duplicates as $dup) {
            echo "Hash: {$dup->hash}, Count: {$dup->count}\n";
        }
        
        echo "Cleaning up duplicates (keeping the latest record for each hash)...\n";
        
        // Удаляем старые дубликаты, оставляя только последнюю запись для каждого hash
        $cleaned = DB::statement("
            DELETE t1 FROM transactions t1
            INNER JOIN transactions t2 
            WHERE t1.hash = t2.hash 
            AND t1.id < t2.id 
            AND t1.hash IS NOT NULL
        ");
        
        echo "Cleanup completed.\n";
        
        // Проверяем результат
        $remainingDuplicates = DB::select("
            SELECT COUNT(*) as count 
            FROM (
                SELECT hash 
                FROM transactions 
                WHERE hash IS NOT NULL 
                GROUP BY hash 
                HAVING COUNT(*) > 1
            ) as duplicates
        ");
        
        $count = $remainingDuplicates[0]->count ?? 0;
        echo "Remaining duplicates: {$count}\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Нельзя восстановить удаленные дубликаты
        echo "Cannot restore deleted duplicate transactions.\n";
    }
};