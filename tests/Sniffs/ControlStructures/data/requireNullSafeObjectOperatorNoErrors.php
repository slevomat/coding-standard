<?php

$a = $b !== false ? $b->doSomething() : null;
$a = false !== $b ? $b->doSomething() : null;

$a = $b === false ? null : $b->doSomething();
$a = false === $b ? null : $b->doSomething();

$a = null !== doSomething() ? true : false;
$a = doSomething() !== null ? true : false;

$a = null === [] ? true : false;

$a = [] === null ? true : false;

$a = $b !== null ? $bb->getC() : null;

$a = $b !== null ? $b->getC() === false : null;

$a = $b === null ? false : true;

$a = $b === null ? null === false : true;

$a = $b === null ? null : [];

$a = $b === null ? null : $b->getC() === false;

$a = $b === null ? null : $bb->getC();

$a = $b !== null && [] === false ? true : false;

if ($a !== null && $a === 0) {
	// Something
}

if ($a === null || $a->property) {
	// Something
}

$a = $b !== null && $b->something !== $bb->isSomething()
	? true
	: false;

$a = $b && $c->getD() !== null ? $c->getD()->getValue() : null;

$a = $b !== false && $c->getD() !== null ? $c->getD()->getValue() : null;

function ($something): bool {
	return $something->getA() !== null && $something->getA()->isBoolean();
};

if ($a === null || $a->size === 0) {
}

if ($a === null || $a->size() === 0) {
}

function format($format, $date): ?string
{
	return $format !== null && $date !== null
		? $date->format($format)
		: null;
}
