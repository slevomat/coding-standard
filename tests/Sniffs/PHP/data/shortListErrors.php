<?php

list($a) = foo();
list($a, $b) = foo();
list(
	list($a, $b),
	$c,
) = foo();
list(
	$a,
	list(
		$b,
		list($c),
		$d,
	),
) = foo();
list(
	'a' => list(
		'b' => list($b),
	),
) = foo();

foreach ($foo as list($a)) {}
foreach (
	$foo as list(
		$a,
		list(
			$b,
			list($c)
		)
	)
) {}

list ($b) = $a;
