<?php // lint >= 7.4

$a = function (? bool$a, ?   string   &$b, ?   int    ...$c)
{

};

function b(
	?bool $a,
	array $b,
	$c
) {

}

fn (? bool$a, ?   string   &$b, ?   int    ...$c) => $a;
