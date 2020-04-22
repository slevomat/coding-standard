<?php

namespace Foo;

use SlevomatCodingStandard\Helpers\ParameterTypeHint;
use function array_filter;
use function array_keys;

anything();

class Whatever
{

	public function in_array($a, $b)
	{
	}

	public static function array_search($a, $b)
	{
	}

}

$whatever = new Whatever();
$whatever->in_array(0, 1);

Whatever::array_search(0, 1);

in_array(0, [], true);
base64_decode('', true);

array_keys(array_filter([], static function (): bool {
	return true;
}));

in_array(0);
array_keys([]);
array_keys(
	[],
);
