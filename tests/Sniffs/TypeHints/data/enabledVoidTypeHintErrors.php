<?php

function func()
{
	return;
}

abstract class VoidClass
{

	/**
	 * @return void
	 */
	abstract public function abstractMethod();

	public function method()
	{
		return;
	}

	/**
	 * @return void
	 */
	public function bothReturnTypeAndAnnotation(): void
	{
		return;
	}

	/**
	 * @return void
	 */
	public function onlyReturnAnnotation()
	{

	}

}

function () {

};

function () {
	return;
};

function () {
	function (): bool {
		return true;
	};
	new class {
		public function foo(): bool
		{
			return true;
		}
	};
};

function (): bool {

};
