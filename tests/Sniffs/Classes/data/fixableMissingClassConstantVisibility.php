<?php

const NON_CLASS = 1;

interface A
{

	const HELLO = 2;

}

class B
{

	const WORLD = 3;

}

class C
{

	const A = 1;
	const B = 2;
	const C = 3;

}

class D
{

	const A = 1, B = 2, C = 3;

}

$class = new class ()
{

	const A = 'a';

};
