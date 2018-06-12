<?php

class FooClass
{

	public function returnsValue()
	{
		return true;
	}

	public function returnsVariable()
	{
		$true = true;
		return $true;
	}

	public function returnsValueInCondition()
	{
		if (true) {
			return true;
		} else {
			return false;
		}
	}

	public function returnsGenerator(): \Generator
	{
		yield [];
	}

	public function returnsGeneratorFrom(): \Generator
	{
		yield from [];
	}

	public function returnsGeneratorWithEarlyTermination(): \Generator
	{
		if (true) {
			yield [];
			return;
		}

		yield [];
	}

	public function returnsGeneratorWithVeryEarlyTermination()
	{
		return;

		yield [];
	}

	public function generatorIsInClosure()
	{
		$x = function (): \Generator {
			yield [];
		};
	}

	public function noReturn()
	{
		// Nothing
	}

	public function returnsVoid()
	{
		return;
	}

	public function containsClosure()
	{
		array_map(function ($item) {
			return $item;
		}, []);
	}

	public function containsAnonymousClass()
	{
		new class
		{

			public function ignore()
			{
				return true;
			}

		};
	}

}
