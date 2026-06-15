<?php

/**
 * FalaqX PHP Framework
 * Entry Point
 */

// Define the root path
define('ROOT_PATH', __DIR__);
define('FALAQ_PATH', ROOT_PATH . '/falaq_x_php');

// Load configuration
require_once ROOT_PATH . '/config.php';

// Boot the framework
require_once FALAQ_PATH . '/startup.php';
