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
