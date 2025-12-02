<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Str;

class UsernameGeneratorService
{
    private array $translitMap = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        'ә' => 'a', 'ғ' => 'g', 'қ' => 'q', 'ң' => 'n', 'ө' => 'o',
        'ұ' => 'u', 'ү' => 'u', 'һ' => 'h', 'і' => 'i',
        'Ә' => 'A', 'Ғ' => 'G', 'Қ' => 'Q', 'Ң' => 'N', 'Ө' => 'O',
        'Ұ' => 'U', 'Ү' => 'U', 'Һ' => 'H', 'І' => 'I',
    ];

    public function generate(string $name, ?int $excludeUserId = null): string
    {
        $username = $this->normalize($name);
        
        if (empty($username)) {
            $username = 'user';
        }

        return $this->makeUnique($username, $excludeUserId);
    }

    public function normalize(string $name): string
    {
        $name = $this->transliterate($name);
        
        $name = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $name);
        
        $name = preg_replace('/\s+/', '_', $name);
        
        $name = preg_replace('/[_-]+/', '_', $name);
        
        $name = trim($name, '_-');
        
        $name = Str::lower($name);
        
        return substr($name, 0, 30);
    }

    private function transliterate(string $text): string
    {
        return strtr($text, $this->translitMap);
    }

    private function makeUnique(string $username, ?int $excludeUserId = null): string
    {
        $originalUsername = $username;
        $counter = 1;
        
        while ($this->usernameExists($username, $excludeUserId)) {
            $suffix = '_' . $counter;
            $maxLength = 30 - strlen($suffix);
            $username = substr($originalUsername, 0, $maxLength) . $suffix;
            $counter++;
            
            if ($counter > 9999) {
                $username = substr($originalUsername, 0, 20) . '_' . uniqid();
                break;
            }
        }

        return $username;
    }

    private function usernameExists(string $username, ?int $excludeUserId = null): bool
    {
        $query = User::where('username', $username);
        
        if ($excludeUserId !== null) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }
}
