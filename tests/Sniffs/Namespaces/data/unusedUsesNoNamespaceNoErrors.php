<?php

use function GuzzleHttp\json_decode;

class Boo
{

	public function foo(string $jsonInput)
	{
		return json_decode($jsonInput);
	}

}
