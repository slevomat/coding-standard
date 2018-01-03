<?php

namespace Boo;

use FooException;

class Foo
{

	public function foo()
	{
		try {
		} catch (\Exception $e) { // ok - catching \Throwable later

		} catch (FooException | \Throwable $e) {

		}

		try {

		} catch (FooException | \Throwable $e) {

		}
	}

}
