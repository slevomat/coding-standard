<?php // lint >= 7.1

namespace FooNamespace;

/**
 * @param string $a
 * @return void
 */
function useless(string $a): void
{

}

/**
 * @param string[] $a
 * @return string[]
 */
function useful(array $a): array
{
	return [];
}

/**
 * @param string $a A
 */
function usefulWithParameterDescription(string $a)
{

}

/**
 * @param string ...$a A
 */
function usefulVaradicWithParameterDescription(string $a)
{

}

/**
 * @param string A
 */
function usefulWithoutParameterNameInAnnotation(string $a)
{

}

/**
 * @return string Decription
 */
function usefulWithReturnDescription(): string
{
	return '';
}

abstract class FooClass
{

	/**
	 * @param int $a
	 * @return int
	 */
	public function publicMethod(int $a): int
	{
		return 0;
	}

	/**
	 * @param bool $a
	 * @return bool
	 */
	protected function protectedMethod(bool $a): bool
	{
		return true;
	}

	/**
	 * @param float $a
	 * @return float
	 */
	private function privateMethod(float $a): float
	{
		return 0.0;
	}

	/**
	 * @param string $a
	 * @return string|null
	 */
	abstract public function abstractMethod(string $a): ?string;

	/**
	 * @param callable $a
	 * @return callable
	 */
	public static function staticMethod(callable $a): callable
	{
		return function (): bool {
			return true;
		};
	}

}
