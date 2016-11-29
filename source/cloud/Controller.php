<?php

namespace Cloud;

class Controller
{
	const CF_NAME = "CloudFrame 2";
	const CF_VERSION = "2.0";
	
	protected function getCFVersion()
	{
		return "v".self::CF_VERSION;
	}
	
	protected function getCFName()
	{
		return self::CF_NAME;
	}
}