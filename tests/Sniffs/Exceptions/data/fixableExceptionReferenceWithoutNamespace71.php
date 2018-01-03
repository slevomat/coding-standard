<?php

class Foo
{

	public function foo()
	{
		try {
		} catch (Exception $e) { // ok - catching \Throwable later

		} catch (FooException | Throwable $e) {

		}

		try {

		} catch (FooException | Exception $e) {

		}
	}

}
