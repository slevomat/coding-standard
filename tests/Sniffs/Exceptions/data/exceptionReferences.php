<?php

namespace FooNamespace;

use Exception as GeneralException;
use SomeOther\Exception;
?><?php
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
		} catch (\Exception $e) { // ok - catching \Throwable later

		} catch (Exception $e) {

		} catch (\Throwable $e) {

		}

		try {

		} catch (\Exception $e) {

		} catch (Exception $e) {

		} catch (GeneralException $e) {

		}
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly.ReferencedGeneralException
	 */
	public function methodFromInterface(\Exception $e)
	{

	}

}
