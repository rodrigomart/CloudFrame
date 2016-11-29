<?php

namespace App\Controllers;

use Cloud\Controller;
use Cloud\Libraries\Http\HttpRequest;
use Cloud\Libraries\Http\HttpResponse;

class Welcome extends Controller
{
	public function helloworld()
	{
		HttpResponse::Header('Content-Type', 'text/html; charset=UTF-8');
		
		$data['Hello'] = 'Olá';
		$data['World'] = 'Mundo!';
		$data['msg'] = 'Bem-vindo ao '.$this->getCFName().'!';
		$data['Version'] = $this->getCFVersion();
		$data['Path'] = BASE_PATH;
		echo view('welcome/helloworld.html', $data);
	}
}
