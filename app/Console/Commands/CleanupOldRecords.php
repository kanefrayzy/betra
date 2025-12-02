<?php
namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupOldRecords extends Command
{
    protected $signature = 'cleanup:old-records';
    protected $description = 'Удаление записей старше месяца чанками';

    const CHUNK_SIZE = 1000;

    public function handle()
    {
        $tables = ['transactions', 'user_game_histories', 'messages', 'notifications'];
        $monthAgo = Carbon::now()->subMonth();

        foreach ($tables as $table) {
            $this->info("Начало очистки таблицы {$table}");

            try {
                $totalDeleted = 0;
                $startTime = microtime(true);

                switch ($table) {
                    case 'transactions':
                        // Получаем диапазон ID для transactions
                        $minId = DB::table($table)
                            ->where('created_at', '<', $monthAgo)
                            ->whereIn('type', ['bet', 'win', 'refund'])
                            ->min('id');

                        $maxId = DB::table($table)
                            ->where('created_at', '<', $monthAgo)
                            ->whereIn('type', ['bet', 'win', 'refund'])
                            ->max('id');

                        if ($minId && $maxId) {
                            $currentId = $minId;

                            while ($currentId <= $maxId) {
                                $iterationStart = microtime(true);

                                $deleted = DB::table($table)
                                    ->where('id', '>=', $currentId)
                                    ->where('id', '<', $currentId + self::CHUNK_SIZE)
                                    ->where('created_at', '<', $monthAgo)
                                    ->whereIn('type', ['bet', 'win', 'refund'])
                                    ->delete();

                                $this->processDeletedRecords($table, $deleted, $currentId, $iterationStart);
                                $totalDeleted += $deleted;
                                $currentId += self::CHUNK_SIZE;
                            }
                        }
                        break;

                        case 'notifications':
                            // Получаем минимальный и максимальный ID для UUID
                            $minId = DB::table($table)
                                ->where('created_at', '<', $monthAgo)
                                ->whereNotNull('read_at')
                                ->orderBy('id', 'asc')
                                ->value('id');

                            $maxId = DB::table($table)
                                ->where('created_at', '<', $monthAgo)
                                ->whereNotNull('read_at')
                                ->orderBy('id', 'desc')
                                ->value('id');

                            if ($minId && $maxId) {
                                // Для UUID будем удалять порциями по CHUNK_SIZE записей
                                do {
                                    $iterationStart = microtime(true);

                                    $idsToDelete = DB::table($table)
                                        ->where('created_at', '<', $monthAgo)
                                        ->whereNotNull('read_at')
                                        ->limit(self::CHUNK_SIZE)
                                        ->pluck('id');

                                    if ($idsToDelete->count() > 0) {
                                        $deleted = DB::table($table)
                                            ->whereIn('id', $idsToDelete)
                                            ->delete();

                                        $this->processDeletedRecords($table, $deleted, 'batch', $iterationStart);
                                        $totalDeleted += $deleted;
                                    } else {
                                        break;
                                    }
                                } while (true);
                            }
                            break;
                    default:
                        // Для остальных таблиц
                        $minId = DB::table($table)
                            ->where('created_at', '<', $monthAgo)
                            ->min('id');

                        $maxId = DB::table($table)
                            ->where('created_at', '<', $monthAgo)
                            ->max('id');

                        if ($minId && $maxId) {
                            $currentId = $minId;

                            while ($currentId <= $maxId) {
                                $iterationStart = microtime(true);

                                $deleted = DB::table($table)
                                    ->where('id', '>=', $currentId)
                                    ->where('id', '<', $currentId + self::CHUNK_SIZE)
                                    ->where('created_at', '<', $monthAgo)
                                    ->delete();

                                $this->processDeletedRecords($table, $deleted, $currentId, $iterationStart);
                                $totalDeleted += $deleted;
                                $currentId += self::CHUNK_SIZE;
                            }
                        }
                }

                $totalTime = microtime(true) - $startTime;
                $this->info("Всего удалено из {$table}: {$totalDeleted} записей за {$totalTime}s");
                Log::info("Очистка {$table} завершена. Удалено записей: {$totalDeleted}. Время: {$totalTime}s");

            } catch (\Exception $e) {
                $this->error("Ошибка при очистке таблицы {$table}: " . $e->getMessage());
                Log::error("Ошибка очистки {$table}: " . $e->getMessage());
            }
        }
    }

    private function processDeletedRecords($table, $deleted, $currentId, $iterationStart)
    {
        if ($deleted > 0) {
            $iterationTime = microtime(true) - $iterationStart;

            // Разные форматы сообщения для notifications и других таблиц
            if ($table === 'notifications') {
                $this->info("Удалено {$deleted} записей из {$table}. Время: {$iterationTime}s");
            } else {
                $this->info("Удалено {$deleted} записей из {$table}. ID: {$currentId} - " . ($currentId + self::CHUNK_SIZE) . ". Время: {$iterationTime}s");
            }

            // Динамическая пауза
            $pause = max(50000, min(200000, 100000 - ($iterationTime * 1000000)));
            usleep($pause);
        }
    }
}
