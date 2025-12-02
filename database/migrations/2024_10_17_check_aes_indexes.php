<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем и добавляем индекс aes_user_code для users
        if (!$this->indexExists('users', 'aes_user_code')) {
            echo "Adding index idx_aes_user_code to users table...\n";
            Schema::table('users', function (Blueprint $table) {
                $table->index('aes_user_code', 'idx_aes_user_code');
            });
        } else {
            echo "Index idx_aes_user_code already exists on users table\n";
        }

        // Проверяем и добавляем уникальный индекс hash для transactions
        if (!$this->indexExists('transactions', 'hash', true)) {
            echo "Adding unique index idx_hash_unique to transactions table...\n";
            Schema::table('transactions', function (Blueprint $table) {
                $table->unique('hash', 'idx_hash_unique');
            });
        } else {
            echo "Unique index idx_hash_unique already exists on transactions table\n";
        }

        // Проверяем и добавляем составной индекс для user_id, created_at
        if (!$this->indexExists('transactions', ['user_id', 'created_at'])) {
            echo "Adding composite index idx_user_created to transactions table...\n";
            Schema::table('transactions', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'idx_user_created');
            });
        } else {
            echo "Index idx_user_created already exists on transactions table\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_aes_user_code');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('idx_hash_unique');
            $table->dropIndex('idx_user_created');
        });
    }

    /**
     * Проверяет существование индекса
     */
    private function indexExists(string $table, $columns, bool $unique = false): bool
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }

        $indexes = DB::select("SHOW INDEX FROM {$table}");
        
        $indexGroups = [];
        foreach ($indexes as $index) {
            $indexGroups[$index->Key_name][] = $index->Column_name;
            if ($unique && !$index->Non_unique) {
                $indexGroups[$index->Key_name]['unique'] = true;
            }
        }

        foreach ($indexGroups as $indexName => $indexColumns) {
            $isUnique = isset($indexColumns['unique']);
            unset($indexColumns['unique']);
            
            if ($indexColumns === $columns) {
                if ($unique && !$isUnique) {
                    continue;
                }
                return true;
            }
        }

        return false;
    }
};