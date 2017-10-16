<?php // lint >= 7.1

namespace FooNamespace;

function useless(string $a): void
{

}

function uselessAligned(string $a, int $b): void
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

	public function publicMethod(int $a): int
	{
		return 0;
	}

	protected function protectedMethod(bool $a): bool
	{
		return true;
	}

	private function privateMethod(float $a): float
	{
		return 0.0;
	}

	abstract public function abstractMethod(string $a): ?string;

	public static function staticMethod(callable $a): callable
	{
		return function (): bool {
			return true;
		};
	}

}
