<?php

namespace Boo;

class Foo
{

	public function foo()
	{
		try {
		} catch (\Exception $e) { // ok - catching \Throwable later

		} catch (\Throwable $e) {

		}

		try {

		} catch (\Exception $e) {

		}
	}

}
