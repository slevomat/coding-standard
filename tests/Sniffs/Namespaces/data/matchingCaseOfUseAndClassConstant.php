<?php declare(strict_types = 1);

namespace Hele\Sms;

use Nette\Http\Url;

class Test
{

	const URL = 'https://foo.com';

	public function sendMessage()
	{
		$url = new Url(self::URL);
		$url->getAbsoluteUrl();
	}

}
