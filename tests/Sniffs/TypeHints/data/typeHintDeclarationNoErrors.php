<?php

namespace FooNamespace;

use UsedNamespace\UsedClass;

abstract class FooClass
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints
	 */
	public function parametersTypeHintsGlobalSuppressed($a, $b)
	{
	}

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
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration
	 */
	public function returnTypeHintGLobalSuppressed()
	{
		return true;
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
	 * @param static $a
	 */
	public function staticAsSelf(self $a)
	{
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
	 * @Assert\Callback()
	 * @param string $a
	 * @param string $b
	 */
	public function withUsefulComplexAnnotation(string $a, string $b)
	{
	}

	/**
	 * @Something\Whatever()
	 * @param string $a
	 * @param string $b
	 */
	public function withUsefulComplexAnnotationOnlyPrefixed(string $a, string $b)
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

	/** @var string[] */
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
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration
	 */
	public $boolWithGlobalSuppress = true;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingPropertyTypeHint
	 */
	public $boolWithSuppress = true;

	/**
	 * @return string[]
	 */
	public function returnTraversableArray(): array
	{
		return [];
	}

	/**
	 * @return iterable|string[]
	 */
	private function returnTraversableIterable(): iterable
	{
		return [];
	}

	/**
	 * @return string[]|\Traversable
	 */
	abstract public function returnTraversableTraversable(): \Traversable;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration
	 * @return \Traversable
	 */
	abstract public function returnTraversableTraversableWithGlobalSuppress(): \Traversable;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableReturnTypeHintSpecification
	 * @return \Traversable
	 */
	abstract public function returnTraversableTraversableWithSuppress(): \Traversable;

	/**
	 * @param string[] $a
	 */
	public function traversableArrayParameter(array $a)
	{
	}

	/**
	 * @param iterable|string[] $a
	 */
	private function taversableIterableParameter(iterable $a)
	{
	}

	/**
	 * @param string[]|\Traversable $a
	 */
	abstract public function traversableTraversableParameter(\Traversable $a);

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration
	 * @param \Traversable $a
	 */
	abstract public function traversableTraversableParameterWithGlobalSuppress(\Traversable $a);

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableParameterTypeHintSpecification
	 * @param \Traversable $a
	 */
	abstract public function traversableTraversableParameterWithSuppress(\Traversable $a);

	/** @var string[] */
	public $traversable = [];

	/** @var string[]|\Traversable|null */
	public $traversableWithMultipleAnnotaton = [];

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration
	 * @var array
	 */
	public $traversableWithGlobalSuppress = [];

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversablePropertyTypeHintSpecification
	 * @var array
	 */
	public $traversableWithSuppress = [];

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

	/**
	 * @param string $a A
	 */
	public function withParameterDescriptionWithParameterNameInAnnotation(string $a)
	{
	}

	/**
	 * @param string ...$a A
	 */
	public function varadicWithParameterDescriptionWithParameterNameInAnnotation(string ...$a)
	{
	}

	/**
	 * @param string A
	 */
	public function withParameterDescriptionWithoutParameterNameInAnnotation(string $a)
	{
	}

	/**
	 * @return string Decription
	 */
	public function withReturnDescription(): string
	{
		return '';
	}

	/**
	 * @return \SomethingElse
	 */
	public function withMoreSpecificReturnAnnotation(): \Something
	{
		return new \Something();
	}

	/**
	 * @param \SomethingElse $a
	 */
	public function withMoreSpecificParameterAnnotation(\Something $a)
	{
	}

	/**
	 * @return bool[][]|array[]
	 */
	private function returnMultidimensionalArray(): array
	{
		return [];
	}

	/**
	 * @return \Traversable[]|mixed[][]
	 */
	private function returnMultidimensionalTraversable(): array
	{
		return [];
	}

	/**
	 * @param bool[][]|array[] $a
	 */
	private function multidimensionalArrayParameter(array $a)
	{
	}

	/**
	 * @param \Traversable[]|mixed[][] $a
	 */
	private function multidimensionalTraversable(array $a)
	{
	}

	/**
	 * @var bool[][]|array[]
	 */
	public $multidimensionalArray = [];

	/**
	 * @var \Traversable[]|mixed[][]
	 */
	private $multidimensionalTraversable = [];

	/**
	 * @var array[]|string[][]|null
	 */
	public $nullableMultidimensionalArray = [];

	/**
	 * @param int[][]|array[]|null $a
	 */
	private function nullableMultidimensionalArrayParameter($a)
	{
	}

	/**
	 * @param
	 * @return
	 */
	abstract public function invalidAnnotations();

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableParameterTypeHintSpecification
	 * @param string|array $a
	 */
	public function mixedContainingTraversable($a)
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableReturnTypeHintSpecification
	 * @return string|array
	 */
	public function returnsMixedContainingTraversable()
	{
		return [];
	}

	/**
	 * @param null|string $string
	 * @param int $int
	 * @param bool|null $bool
	 * @param float $float
	 * @param callable|null $callable
	 * @param mixed[] $array
	 * @param \FooNamespace\FooClass|null $object
	 * @return mixed
	 */
	abstract public function parametersWithWeirdDefinition(?string$string,int$int,?bool$bool=true,float$float,?callable$callable,array$array=[],?\FooNamespace\FooClass$object);

}
