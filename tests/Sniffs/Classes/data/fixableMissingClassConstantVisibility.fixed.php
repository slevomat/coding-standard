<?php

const NON_CLASS = 1;

interface A
{

	public const HELLO = 2;

}

class B
{

	public const WORLD = 3;

}

class C
{

	public const A = 1;
	public const B = 2;
	public const C = 3;

}

class D
{

	public const A = 1, B = 2, C = 3;

}

$class = new class ()
{

	public const A = 'a';

};
