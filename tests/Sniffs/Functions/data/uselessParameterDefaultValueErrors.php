<?php // lint >= 7.4

function a(?int $a = null, $aa)
{

}

function b(
	$b,
	?int $bb = null,
	$bbb = true,
	$bbbb
) {

}

function c($c = true, $cc, ...$ccc)
{

}

fn ($d = true, $dd, ...$ddd) => $d;
