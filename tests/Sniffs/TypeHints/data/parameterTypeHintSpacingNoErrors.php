<?php // lint >= 8.1

$a = function (?bool $a, ?string &$b, ?int ...$c)
{

};

function b(
	?bool $a,
	array $b,
	#[SomeAttribute]
	$c,
	$d
) {

}

fn (?bool $a, ?string &$b, ?int ...$c) => $a;

array_map(
	static fn (array $value): array => array_filter($value),
	[]
);

fn (int|false $a, null|string $b) => $a;
