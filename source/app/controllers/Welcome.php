<?php

namespace App\Controllers;

use Cloud\Libraries\Http\HttpRequest;
use Cloud\Libraries\Http\HttpResponse;


class Welcome
{
	public function helloworld()
	{
		HttpResponse::Header('Content-Type', 'text/html; charset=UTF-8');
		
		$data['Hello'] = 'Olá';
		$data['World'] = 'Mundo!';
		$data['msg'] = 'Bem-vindo ao CloudFrame 2!';
		$data['Version'] = 'v'.CF_VERSION;
		echo view('welcome/helloworld.html', $data);
	}
}
