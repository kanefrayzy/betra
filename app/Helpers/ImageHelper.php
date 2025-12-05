<?php

if (!function_exists('webp_url')) {
    /**
     * Get WebP URL if exists, otherwise return original
     */
    function webp_url(string $url): string
    {
        if (empty($url)) {
            return $url;
        }

        // Если это внешний URL, возвращаем как есть
        if (str_starts_with($url, 'http')) {
            return $url;
        }

        // Заменяем расширение на .webp
        $webpUrl = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $url);
        
        // Проверяем существование WebP файла
        if (file_exists(public_path($webpUrl))) {
            return $webpUrl;
        }

        return $url;
    }
}

if (!function_exists('picture_tag')) {
    /**
     * Generate picture tag with WebP source
     */
    function picture_tag(string $src, string $alt = '', string $class = '', array $attributes = []): string
    {
        $webpSrc = webp_url($src);
        $attrs = '';
        
        foreach ($attributes as $key => $value) {
            $attrs .= " {$key}=\"{$value}\"";
        }

        $html = '<picture>';
        
        if ($webpSrc !== $src && pathinfo($webpSrc, PATHINFO_EXTENSION) === 'webp') {
            $html .= '<source srcset="' . asset($webpSrc) . '" type="image/webp">';
        }
        
        $html .= '<img src="' . asset($src) . '" alt="' . htmlspecialchars($alt) . '"';
        
        if ($class) {
            $html .= ' class="' . $class . '"';
        }
        
        $html .= $attrs . '>';
        $html .= '</picture>';
        
        return $html;
    }
}
