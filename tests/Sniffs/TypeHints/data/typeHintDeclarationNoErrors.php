<?php

namespace FooNamespace;

use UsedNamespace\UsedClass;

abstract class FooClass
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function parametersTypeHintsSuppressed($a, $b)
	{
	}

	public function noParameters()
	{
	}

	public function allParametersWithTypeHint(string $a, int $b)
	{
	}

	/**
	 * @param mixed $a
	 */
	public function mixedParameter($a)
	{
	}

	/**
	 * @param mixed[]|array $a
	 */
	public function mixedTraversableParameter(array $a)
	{
	}

	/**
	 * @param string|integer $a
	 */
	public function moreTypesParameter($a)
	{
	}

	/**
	 * @param null $a
	 */
	public function nullParameter($a)
	{
	}

	/**
	 * @param string[] $a
	 */
	public function arrayParameter(array $a)
	{
	}

	/**
	 * @param string[] $a
	 */
	public function traversableParameter(\Traversable $a)
	{
	}

	/**
	 * @param \FooClass[]|\QueryResultSet $a
	 */
	public function moreTypeWithTraversableParameter(\QueryResultSet $a)
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function returnTypeHintSuppressed()
	{
		return true;
	}

	abstract function abstractFunction();

	public function withoutReturn()
	{
		// Nothing
	}

	public function returnsVoid()
	{
		return;
	}

	public function hasReturnTypeHint(): bool
	{
		return true;
	}

	/**
	 * @return mixed
	 */
	public function mixedReturnValue()
	{
		if (true) {
			return true;
		}

		return 0;
	}

	/**
	 * @return mixed[]
	 */
	abstract public function mixedTraversableReturnValue(): array;

	/**
	 * @return string|integer
	 */
	public function returnsMoreType()
	{
		return 0;
	}

	/**
	 * @return string[]
	 */
	public function returnsArray(): array
	{
		return [];
	}

	/**
	 * @return string[]
	 */
	public function returnsTraversable(): \Traversable
	{
		return [];
	}

	/**
	 * @return string[]|\QueryResultSet
	 */
	public function returnsMoreTypesWithTraversable(): \QueryResultSet
	{
		return [];
	}

	/**
	 * @return static
	 */
	public function returnsStaticAsSelf(): self
	{
		return $this;
	}

	/**
	 * @return $this
	 */
	public function returnsThisAsSelf(): self
	{
		return $this;
	}

	/**
	 * @return string[]|\UsedNamespace\UsedClass
	 */
	abstract public function returnUsedClass(): UsedClass;

	/**
	 * @return string[]|\FooNamespace\ClassFromCurrentNamespace
	 */
	abstract function returnClassFromCurrentNamespace(): ClassFromCurrentNamespace;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessDocComment
	 * @param string $a
	 * @param string $b
	 */
	public function uselessCommentSuppressed(string $a, string $b)
	{
	}

	public function noDocComment()
	{
	}


	/**
	 * Description
	 *
	 * @param string $a
	 * @param string $b
	 */
	public function withDocCommentDescription(string $a, string $b)
	{
	}

	/**
	 * @see https://www.slevomat.cz
	 * @param string $a
	 * @param string $b
	 */
	public function withUsefulAnnotation(string $a, string $b)
	{
	}

	/**
	 * @return mixed
	 */
	abstract public function withMixedReturnAnnotation();

	/**
	 * @phpcsSuppress Whatever
	 * @param string $s
	 */
	public function withAnySuppress(string $s)
	{
	}

	/**
	 * @return FooClass|\PHPUnit_Framework_MockObject_MockObject
	 */
	public function withMock(): FooClass
	{
		return $this;
	}

	/**
	 * @param string|int|null $value
	 */
	public function returnTypeHintIsNotChecked($value): self
	{
		return $this;
	}

	/** @var bool */
	private $boolean = true;

	/** @var array */
	public $array = [];

	/**
	 * @return int|string|\DateTimeImmutable
	 */
	public function moreTypeHints()
	{
		return 0;
	}

	/**
	 * @return string[]|null[]|array
	 */
	public function mixedArrayReturnValue(): array
	{
		return [];
	}

	/**
	 * @return string[]|null
	 */
	public function returnsNullableArrayOfStrings()
	{
		return [];
	}

	/**
	 * @return resource
	 */
	public function returnsResource()
	{
		return fopen('test.log', 'rb');
	}

	/**
	 * @return null
	 */
	public function returnsNull()
	{
		return null;
	}

	/**
	 * @param resource $a
	 */
	public function parameterResource($a)
	{

	}

	/**
	 * @param true $a
	 */
	public function parameterTrue($a)
	{

	}

	/**
	 * @param mixed|null $a
	 */
	public function nullableMixed($a)
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingPropertyTypeHint
	 */
	public $bool = true;

	/**
	 * @param string|int Description
	 * @param \Traversable|string[]
	 */
	public function parametersWithoutTypeHintAndWithAnnotationWithoutParameterName($a, \Traversable $b)
	{
	}

	/**
	 * @param string $b
	 * @param \Traversable|string[]
	 * @param float $c
	 */
	public function oneParameterWithoutTypeHintAndWithAnnotationWithoutParameterName(string $a, \Traversable $b, float $c)
	{
	}

}
