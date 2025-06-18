<?php

/**
 * Determines if the current environment is a local environment.
 *
 * This function checks the server's hostname to identify if it matches
 * common patterns for local development environments, such as 'localhost',
 * '127.0.0.1', or domains ending with '.local' or '.test'.
 *
 * @return bool True if the environment is local, false otherwise.
 */
function isLocalEnvironment()
{
    // Get the server hostname
    $hostname = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';

    // Check if it's a local environment
    return in_array($hostname, ['localhost', '127.0.0.1']) ||
        strpos($hostname, '.local') !== false ||
        strpos($hostname, '.test') !== false;
}

/**
 * Get the base URL
 *
 * @return string The base URL for the application
 */
function baseUrl($path = '')
{
    if (defined('BASE_URL')) {
        return constant('BASE_URL') . ltrim($path, '/');
    }

    // Set the protocol based on HTTPS status
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

    // Get the server hostname
    $hostname = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';

    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

    return $protocol . $hostname . $base . '/' . ltrim($path, '/');
}

function basePath($path = '')
{
    return realpath(__DIR__ . '/../' . '/' . ltrim($path, '/'));
}

function route($name, $params = [])
{
    return baseUrl(Router::route($name, $params));
}

function viewsPath($path = '')
{
    return basePath('app/views/' . ltrim($path, '/'));
}

function redirect($path = '', $queryParams = [])
{
    $url = baseUrl($path);
    if (!empty($queryParams)) {
        $queryString = http_build_query($queryParams);
        $url .= '?' . $queryString;
    }

    header("Location: " . $url);
    exit;
}

function config($key)
{
    static $config = null;

    if ($config === null) {
        $config = require basePath('config/config.php');
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return null;
        }
        $value = $value[$k];
    }

    return $value;
}

function render($view, $data = [], $layout = 'layout')
{

    extract($data);

    ob_start();

    require_once viewsPath($view . '.php');

    $content = ob_get_clean();

    require_once viewsPath($layout . '.php');
}

/**
 * Safely escape output to prevent XSS attacks
 *
 * @param mixed $value The value to be escaped
 * @param string $context The context in which the value will be used (html, js, attr, url)
 * @return string The escaped value
 */
function e($value, $context = 'html')
{
    if ($value === null) {
        return '';
    }

    $value = (string) $value;

    switch ($context) {
        case 'html':
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        case 'js':
            // For JavaScript context, additional escaping may be needed
            return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        case 'attr':
            // For HTML attributes
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        case 'url':
            // For URLs
            return urlencode($value);
        default:
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

function isLoggedIn()
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function getUserFullName()
{
    if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
        if (isset($_SESSION['user']['first_name']) && isset($_SESSION['user']['last_name'])) {
            return $_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name'];
        }

        return $_SESSION['user']['username'] ?? 'Guest';
    }

    return 'Guest';
}
