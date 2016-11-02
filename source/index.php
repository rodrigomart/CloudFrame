<?php

define('CF_VERSION', '2.0');
define('BASE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

# Autoloader PSR-4
require_once BASE_PATH.'cloud/autoload.php';

# Using CLOUD
use Cloud\Cloud;

# Cloud frame
$cloud = new Cloud(['debug'=>false]);

require_once BASE_PATH.'app/routes.php';

# Run application
$cloud->Run();