<?php

function foo(FooException $e)
{
	try {
		throw new FooException();
	} catch (BarException $ex) {

	}
}

function bar(\Some\Exception $e)
{
	try {
		throw new \Some\Other\Exception();
	} catch (\Some\Other\DifferentException $ex) {

	}
}
