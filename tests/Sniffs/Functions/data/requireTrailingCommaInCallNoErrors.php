<?php // lint >= 7.4

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
	$b,
);

if (true) {
	(function () {
	})();
}

if (
	(function () {
	})()
) {

}

$array = [
	(function () {
	})()
];

$array[
	(function () {
	})()
] = true;

doSomething([
	$a,
]);

doSomething( $a,
	$b );

doSomething( doSomething(
	$foo,
) );

$array = [];
usort($array, static fn ($a, $b): int
	=> (int) $b->getType() <=> (int) $a->getType()
		?: $a->getId() <=> $b->getId()
);
