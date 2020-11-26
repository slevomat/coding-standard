<?php

use SlevomatCodingStandard\Helpers\Comment;

max(0, 1);

function (
	$a,
	$b
) {
};

class Whatever
{

	public function __construct(
		$something,
		$anything
	)
	{
	}

}

$array = array(
	$a,
	$b
);

if (
	$boolean
) {

}

doSomething(
);

doAnything(
	$a,
	$b
);

if (true) {
	(function () {
	})(true, false);
}

if (
	(function () {
	})(0, [])
) {

}

return array_map(static function ($a): string {
	return $a;
}, []);

doSomething([
	$a
]);
