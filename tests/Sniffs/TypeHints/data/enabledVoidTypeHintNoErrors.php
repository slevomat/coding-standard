<?php // lint >= 7.1

function func(): void
{
	return;
}

abstract class VoidClass
{

	public function __construct()
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessDocComment
	 * @return void
	 */
	public function __destruct()
	{

	}

	public function __clone()
	{

	}

	abstract public function abstractMethod(): void;

	public function method(): void
	{
		return;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function withSuppress()
	{
		return;
	}

}

function (): void {

};

function (): void {
	return;
};

function (): void {
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

function () {
	return true;
};

function (): bool {
	return true;
};
