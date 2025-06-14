<?php

/**
 * Get the base URL based on environment
 *
 * @return string The base URL for the application
 */
function getBaseUrl()
{
  // Get the server hostname
  $hostname = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';

  // Check if it's a local environment
  $isLocal = in_array($hostname, ['localhost', '127.0.0.1']) ||
    strpos($hostname, '.local') !== false ||
    strpos($hostname, '.test') !== false;

  // Set the protocol based on HTTPS status
  $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';

  return $protocol . $hostname . ($isLocal ? '/mvc-app/public/' : '/');
}

// Define the BASE_URL constant
// Using define() instead of const to allow for conditional expressions
define('BASE_URL', getBaseUrl());
