<?php
# Autoloader
spl_autoload_register(function($class){
	$class = str_replace(
		array('/','\\'),
		DIRECTORY_SEPARATOR,
		$class
	);
	
	$className  = $class;
	$namespaces = '';
	
	if($lastPos = strrpos($class, DIRECTORY_SEPARATOR)):
		$className = substr($class, $lastPos+1);
		$namespace = strtolower(substr($class, 0, $lastPos+1));
	endif;
	
	require $namespace.$className.'.php';
});

#
# Loading Cloud 
#
$sep = DIRECTORY_SEPARATOR;
require_once BASE_PATH.'cloud'.$sep.'config'.$sep.'autoload.php';

# Loading Helpers
foreach($autoload['helper'] as $helper)
	require_once BASE_PATH.'cloud'.$sep.'helpers'.$sep.$helper.'.php';

#
# Loading Application
#
require_once BASE_PATH.'app'.$sep.'config'.$sep.'autoload.php';

# Loading Helpers
foreach($autoload['helper'] as $helper)
	require_once BASE_PATH.'app'.$sep.'helpers'.$sep.$helper.'.php';
