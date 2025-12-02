<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DeployController extends Controller
{
    /**
     * GitHub Webhook handler для автодеплоя
     */
    public function webhook(Request $request)
    {
        // Проверяем секретный токен GitHub
        $secret = config('deploy.github_secret');
        $signature = $request->header('X-Hub-Signature-256');
        
        if ($signature) {
            $payload = $request->getContent();
            $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);
            
            if (!hash_equals($hash, $signature)) {
                Log::warning('GitHub webhook: Invalid signature');
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }
        
        // Получаем данные события
        $event = $request->header('X-GitHub-Event');
        $payload = $request->all();
        
        // Логируем запрос
        Log::info('GitHub webhook received', [
            'event' => $event,
            'ref' => $payload['ref'] ?? null,
            'commit_message' => $payload['head_commit']['message'] ?? null,
        ]);
        
        // Обрабатываем только push события в main ветку
        if ($event !== 'push') {
            return response()->json(['message' => 'Event ignored, only push events are processed']);
        }
        
        if (($payload['ref'] ?? '') !== 'refs/heads/main') {
            return response()->json(['message' => 'Push ignored, only main branch is deployed']);
        }
        
        // Проверяем сообщение коммита
        $commitMessage = $payload['head_commit']['message'] ?? '';
        
        // Деплоим только если коммит начинается с "Fix"
        if (!str_starts_with($commitMessage, 'Fix')) {
            Log::info('Deploy skipped: commit message does not start with "Fix"', [
                'commit_message' => $commitMessage
            ]);
            
            return response()->json([
                'message' => 'Deploy skipped: commit message must start with "Fix"',
                'commit_message' => $commitMessage
            ]);
        }
        
        Log::info('Starting deployment', [
            'commit' => $payload['head_commit']['id'] ?? null,
            'author' => $payload['head_commit']['author']['name'] ?? null,
            'message' => $commitMessage
        ]);
        
        // Запускаем деплой в фоне
        $this->runDeploy($payload);
        
        return response()->json([
            'message' => 'Deployment started',
            'commit' => $payload['head_commit']['id'] ?? null,
            'commit_message' => $commitMessage
        ]);
    }
    
    /**
     * Запуск git pull
     */
    private function runDeploy(array $payload)
    {
        $deployScript = config('deploy.script_path');
        
        if (!file_exists($deployScript)) {
            Log::error('Deploy script not found', ['path' => $deployScript]);
            return;
        }
        
        // Запускаем git pull асинхронно
        dispatch(function () use ($deployScript) {
            try {
                $logFile = storage_path('logs/github.log');
                
                $this->logToFile($logFile, 'Running git pull');
                
                $process = Process::fromShellCommandline("bash $deployScript 2>&1");
                $process->setTimeout(60);
                $process->run();
                
                if ($process->isSuccessful()) {
                    $this->logToFile($logFile, 'Git pull completed');
                    $this->logToFile($logFile, 'Output: ' . $process->getOutput());
                } else {
                    $this->logToFile($logFile, 'Git pull failed');
                    $this->logToFile($logFile, 'Error: ' . $process->getErrorOutput());
                }
                
            } catch (\Exception $e) {
                $this->logToFile(storage_path('logs/github.log'), 'Exception: ' . $e->getMessage());
            } 
        })->afterResponse();
    }
     
    /**
     * Логирование в файл github.log
     */
    private function logToFile(string $filePath, string $message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($filePath, $logMessage, FILE_APPEND);
    }
}
