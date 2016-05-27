<?php

function foo(FooException $e)
{
	try {
		throw new FooException();
	} catch (BarException $ex) {

	} catch (Throwable $ex) {

	} catch (TypeError $ex) {

	}
}

function bar(\Some\Exception $e)
{
	try {
		throw new \Some\Other\Exception();
	} catch (\Some\Other\DifferentException $ex) {

	} catch (\Throwable $ex) {

	} catch (\Exception $ex) {

	} catch (\TypeError $ex) {

	} catch (\Foo\BarError $ex) {

	}
}
