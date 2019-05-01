<?php

$abc = 'abc';

$a = <<<HED
	$abc
HED;

$b = <<<"HED"
	$abc
HED;

$c = <<<"HED"
	{$abc}
HED;
