<?php
# Autoloader
require 'autoload';


# Using CLOUD
use Cloud\Cloud;
use Cloud\Http\HttpRequest;
use Cloud\Http\HttpResponse;


# Cloud frame
$cloud = new Cloud;

# Nat
$cloud->Route('nat/(:any)', function($data){
	HttpResponse::Content(
		$data.': '.
		HttpRequest::Address().':'.HttpRequest::Port()
	);
});

# Run application
$cloud->Run();
