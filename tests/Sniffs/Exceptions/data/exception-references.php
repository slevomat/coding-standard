<?php

namespace FooNamespace;

use Exception as GeneralException;
use SomeOther\Exception;

class Foo extends \Exception
{

	public function __construct(
		\Exception $e,
		Exception $concreteException,
		Throwable $previous = null
	)
	{
		new \Exception('foo');
		if ($e instanceof \Exception) {

		}
		try {
			foo();
		} catch (\Exception $e) {

		} catch (Exception $e) {

		} catch (\Throwable $e) {

		}
	}

}
