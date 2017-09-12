<?php

class Test
{

	public function __construct()
	{
		$this
			->testMethod1()
			->testMethod2()
			->testMethod3()
		;
	}

	private function testMethod1()
	{
		return $this;
	}

	private function testMethod2()
	{
		return $this;
	}

	private function testMethod3()
	{
		return $this;
	}

}
