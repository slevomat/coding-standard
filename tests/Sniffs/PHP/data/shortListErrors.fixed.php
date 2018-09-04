<?php

[$a] = foo();
[$a, $b] = foo();
[
	[$a, $b],
	$c,
] = foo();
[
	$a,
	[
		$b,
		[$c],
		$d,
	],
] = foo();
[
	'a' => [
		'b' => [$b],
	],
] = foo();

foreach ($foo as [$a]) {}
foreach (
	$foo as [
		$a,
		[
			$b,
			[$c]
		]
	]
) {}

[$b] = $a;
