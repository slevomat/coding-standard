<?php

abstract class FooClass
{

	public function parametersWithoutTypeHints($a)
	{
	}

	/**
	 * @see https://www.slevomat.cz
	 */
	public function parametersWithoutTypeHintsAndWithDocComment($a)
	{
	}

	/**
	 * @param string $a
	 */
	public function parameterWithoutTypeHintAndWithSimpleParamAnnotation($a)
	{
	}

	/**
	 * @param string|null $a
	 */
	public function parameterWithoutTypeHintAndWithSimpleNullableParamAnnotation($a)
	{
	}

	/**
	 * @param string[] $a
	 */
	public function parameterWithoutTypeHintAndWithArrayAnnotation($a)
	{
	}

	/**
	 * @param \FooClass[]|\Traversable $a
	 */
	public function parameterWithoutTypeHintAndWithTraversableAnnotation($a)
	{
	}

	public function withoutReturnTypeHint()
	{
		return true;
	}

	/**
	 * @see https://www.slevomat.cz
	 */
	public function withoutReturnTypeHintAndWithDocComment()
	{
		return true;
	}

	/**
	 * @return boolean
	 */
	public function withoutReturnTypeHintAndWithSimpleReturnAnnotation()
	{
		return true;
	}

	/**
	 * @return string[]
	 */
	public function withoutReturnTypeHintAndWithArrayReturnAnnotation()
	{
		return true;
	}

	/**
	 * @return string[]|\Traversable
	 */
	public function withoutReturnTypeHintAndWithTraversableReturnAnnotation()
	{
		return true;
	}

	/**
	 * @return
	 */
	public function withoutReturnTypeHintAndWithInvalidReturnAnnotation()
	{
		return true;
	}

	/**
	 * @param string $a
	 */
	public function uselessDocCommentBecauseOfParameterHint(string $a)
	{
	}

	/**
	 * @return boolean
	 */
	abstract public function uselessDocCommentBecauseOfReturnTypeHint(): bool;

}
