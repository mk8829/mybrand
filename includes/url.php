<?php
if (!function_exists('base_url')) {
    function base_url(): string
    {
        $configuredBaseUrl = defined('BASE_URL') ? (string) constant('BASE_URL') : '';
        if ($configuredBaseUrl !== '') {
            $parts = parse_url($configuredBaseUrl);
            if (is_array($parts) && !empty($parts['scheme']) && !empty($parts['host'])) {
                $port = isset($parts['port']) ? ':' . (string) $parts['port'] : '';
                return (string) $parts['scheme'] . '://' . (string) $parts['host'] . $port;
            }

            return rtrim($configuredBaseUrl, '/');
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
            || ((int) ($_SERVER['SERVER_PORT'] ?? 80) === 443);
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host;
    }
}

if (!function_exists('app_base_path')) {
    function app_base_path(): string
    {
        $configuredBasePath = defined('BASE_PATH') ? (string) constant('BASE_PATH') : '';
        $configuredBaseUrl = defined('BASE_URL') ? (string) constant('BASE_URL') : '';

        if ($configuredBasePath !== '') {
            $basePath = trim($configuredBasePath, '/');
            return $basePath === '' ? '' : '/' . $basePath;
        }

        if ($configuredBaseUrl !== '') {
            $path = parse_url($configuredBaseUrl, PHP_URL_PATH);
            if (is_string($path) && trim($path, '/') !== '') {
                return '/' . trim($path, '/');
            }
        }

        $documentRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));
        $projectRoot = realpath(__DIR__ . '/..');

        if ($documentRoot && $projectRoot) {
            $documentRoot = str_replace('\\', '/', rtrim($documentRoot, '/'));
            $projectRoot = str_replace('\\', '/', rtrim($projectRoot, '/'));

            if (stripos($projectRoot, $documentRoot) === 0) {
                $basePath = substr($projectRoot, strlen($documentRoot));
                $basePath = trim((string) $basePath, '/');
                return $basePath === '' ? '' : '/' . $basePath;
            }
        }

        return '';
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim(base_url() . app_base_path(), '/');

        if ($path === '') {
            return $base . '/';
        }

        if (preg_match('#^(https?:)?//#i', $path)) {
            return $path;
        }

        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('why_page_url')) {
    function why_page_url(string $slug): string
    {
        $slug = trim($slug, "/ \t\n\r\0\x0B");
        return url($slug);
    }
}
