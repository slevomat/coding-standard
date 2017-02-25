<?php // lint >= 7.1

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
