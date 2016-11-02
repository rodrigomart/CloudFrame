<?php

/*
| -------------------------------------------------------------------
|  Utilities Helper
| -------------------------------------------------------------------
|  @author Cesar Ferreira
*/

/*
| -------------------------------------------------------------------
|  View
| -------------------------------------------------------------------
| Reads a file and replace its contents with a given array.
|
| @access	public
| @param	$view string
| @param	$data array
| @return	string
*/
if (!function_exists('view'))
{
	function view($view, $data = '')
	{
		$content = '';
		$content = file_get_contents(BASE_PATH.'app/views/'.$view);
		$find = $replace = [];
		
		foreach($data as $key => $val)
		{
			array_push($find, '{'.$key.'}');
			array_push($replace, $val);
		}
		$content = str_ireplace($find, $replace, $content);
		
		return $content;
	}
}
