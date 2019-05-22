<?php

/**
 *
 */
class EmptyDocComment
{

}

/**
 * @see Anything
 */
interface DifferentAnnotation
{

}

/**
 * Summary
 *
 * {@inheritDoc}
 */
trait ContainsEvenSomethingElse
{

}

class NoErrors
{

	/**
	 * {@inheritdoc}
	 */
	public function parameterWithoutTypeHint($a): bool
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function iterableParameter(array $a, iterable $b): bool
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withoutReturnType()
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function iterableReturnType(): array
	{
	}

	/**
	 * {@inheritdoc}
	 */
	protected $propertiesAreNotSupported;
}
