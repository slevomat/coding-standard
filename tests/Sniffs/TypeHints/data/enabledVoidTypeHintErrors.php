<?php // lint >= 7.1

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
	public function withSuppress(): void
	{
		return;
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
