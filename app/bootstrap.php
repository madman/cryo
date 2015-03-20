<?php

require_once __DIR__ . '/../vendor/autoload.php';

defined('CORE_ROOT_DIR') or define('CORE_ROOT_DIR', __DIR__ . '/..');
defined('CORE_APP_DIR') or define('CORE_APP_DIR', CORE_ROOT_DIR . '/app');
defined('CORE_CONFIG_DIR') or define('CORE_CONFIG_DIR', CORE_ROOT_DIR. '/app/config');
defined('CORE_WEB_DIR') or define('CORE_WEB_DIR', CORE_ROOT_DIR. '/web');
defined('CORE_RUNTIME_DIR') or define('CORE_RUNTIME_DIR', CORE_ROOT_DIR. '/runtime');
defined('CORE_UPLOAD_DIR') or define('CORE_UPLOAD_DIR', CORE_WEB_DIR. '/uploads');
defined('CORE_UPLOADS_URL') or define('CORE_UPLOADS_URL', '/uploads');

$app = new \Core\Application;
$app->initialize();

return $app;
