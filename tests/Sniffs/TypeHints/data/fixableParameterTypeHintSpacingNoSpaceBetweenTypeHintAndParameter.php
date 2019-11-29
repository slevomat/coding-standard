<?php // lint >= 7.4

$a = function (?bool$a, ?string&$b, ?int...$c)
{

};

fn (?bool$a, ?string&$b, ?int...$c) => $a;

function b(
	?bool$a,
	array$b,
	$c
) {

}
