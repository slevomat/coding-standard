<?php

class Foo
{

	public function foo()
	{
		try {
		} catch (Exception $e) { // ok - catching \Throwable later

		} catch (Throwable $e) {

		}

		try {

		} catch (\Throwable $e) {

		}
	}

}
