<?php

use AnyNamespace\Anything;
use AnyNamespace\Traversable;
use Doctrine;
use Doctrine\Common\Collections as DoctrineCollections;

abstract class FooClass
{

	public function parametersWithoutTypeHints($a): void
	{
	}

	/**
	 * @see https://www.slevomat.cz
	 */
	public function parametersWithoutTypeHintsAndWithDocComment($a): void
	{
	}

	/**
	 * @param string $a
	 */
	public function parameterWithoutTypeHintAndWithSimpleParamAnnotation($a): void
	{
	}

	/**
	 * @param string|null $a
	 */
	public function parameterWithoutTypeHintAndWithSimpleNullableParamAnnotation($a): void
	{
	}

	/**
	 * @param string[] $a
	 */
	public function parameterWithoutTypeHintAndWithArrayAnnotation($a): void
	{
	}

	/**
	 * @param \FooClass[]|\Traversable $a
	 */
	public function parameterWithoutTypeHintAndWithTraversableAnnotation($a): void
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
	 * @return bool
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
	public function uselessDocCommentBecauseOfParameterHint(string $a): void
	{
	}

	/**
	 * @return bool
	 */
	abstract public function uselessDocCommentBecauseOfReturnTypeHint(): bool;

	private $boolean = true;

	public $array = [];

	/**
	 * @return mixed[]
	 */
	public function traversableMixedReturnTypeHint()
	{
		return [];
	}

	/**
	 * @param mixed[] $a
	 */
	abstract public function traversableMixedParameterTypeHint($a): void;

	public function returnTraversableArrayWithoutAnnotation(): array
	{
		return [];
	}

	private function returnTraversableIterableWithoutAnnotation(): iterable
	{
		return [];
	}

	abstract public function returnTraversableTraversableWithoutAnnotation(): \Traversable;

	/**
	 * @return array
	 */
	public function returnTraversableWithUnsufficientSingleAnnotation(): array
	{
		return [];
	}

	/**
	 * @return array|iterable|\Traversable
	 */
	public function returnTraversableWithUnsufficientMultipleAnnotation(): iterable
	{
		return [];
	}

	public function traversableArrayParameterWithoutAnnotation(array $a): void
	{
	}

	private function traversableIterableParameterWithoutAnnotation(iterable $a): void
	{
	}

	abstract public function traversableTraversableParameterWithoutAnnotation(\Traversable $a): void;

	/**
	 * @param array $a
	 */
	public function traversableParameterWithUnsufficientSingleAnnotation(array $a): void
	{
	}

	/**
	 * @param array|iterable|\Traversable $a
	 */
	public function traversableParameterWithUnsufficientMultipleAnnotation(iterable $a): void
	{
	}

	/** @var array */
	public $traversableWithUnsufficientSingleAnnotation = [];

	/** @var array|\Traversable|null */
	public $traversableWithUnsufficientMultipleAnnotation = [];

	/**
	 * @return array|null
	 */
	public function returnTraversableOrNull()
	{
		return [];
	}

	/**
	 * @param array|null $a
	 */
	public function traversableNullableParameter($a): void
	{
	}

	/**
	 * @return \Something
	 */
	public function returnTypeHintEqualsAnnotationBothFullyQualified(): \Something
	{
		return new \Something();
	}

	/**
	 * @return \AnyNamespace\Anything
	 */
	public function returnTypeHintEqualsAnnotationWithOnlyAnnotationFullyQualified(): Anything
	{
		return new Anything();
	}

	/**
	 * @return Anything
	 */
	public function returnTypeHintEqualsAnnotationWithOnlyTypeHintFullyQualified(): \AnyNamespace\Anything
	{
		return new Anything();
	}

	/**
	 * @param \Something $a
	 */
	public function parameterTypeHintEqualsAnnotationBothFullyQualified(\Something $a): void
	{
	}

	/**
	 * @param \AnyNamespace\Anything $a
	 */
	public function parameterTypeHintEqualsAnnotationWithOnlyAnnotationFullyQualified(Anything $a): void
	{
	}

	/**
	 * @param Anything $a
	 */
	public function parameterTypeHintEqualsAnnotationWithOnlyTypeHintFullyQualified(\AnyNamespace\Anything $a): void
	{
	}

	/**
	 * @param \DateTime|\DateTimeImmutable $a
	 */
	public function multipleParameterTypeHints(\DateTimeInterface $a): void
	{
	}

	/**
	 * @return array[]
	 */
	private function returnMultidimensionalArray(): array
	{
		return [];
	}

	/**
	 * @return \Traversable[]
	 */
	private function returnMultidimensionalTraversable(): array
	{
		return [];
	}

	/**
	 * @param array[] $a
	 */
	private function multidimensionalArrayParameter(array $a): void
	{
	}

	/**
	 * @param \Traversable[] $a
	 */
	private function multidimensionalTraversable(array $a): void
	{
	}

	/**
	 * @var array[][]
	 */
	public $multidimensionalArray = [];

	/**
	 * @var \Traversable[]
	 */
	private $multidimensionalTraversable = [];

	/**
	 * @var array[]|null
	 */
	public $nullableMultidimensionalArray = [];

	/**
	 * @return array[]|null
	 */
	private function returnNullableMultidimensionalArray()
	{
		return [];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param array[]|null $a
	 */
	private function nullableMultidimensionalArrayParameter($a): void
	{
	}

	private function returnUsedTraversable(): Traversable
	{

	}

	public function usedTraversableParameter(Traversable $a): void
	{

	}

	/** @var Traversable[] */
	private $usedTraversable = [];

	/**
	 * @var Doctrine\Common\Collections\ArrayCollection
	 */
	protected $partialUseTraversable;

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $a
	 */
	private function partialUseTraversableParameter(Doctrine\Common\Collections\ArrayCollection $a): void
	{

	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function returnpartialUseTraversable(): Doctrine\Common\Collections\ArrayCollection
	{
		return new Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @var DoctrineCollections\ArrayCollection
	 */
	protected $partialUseWithAliasTraversable;

	/**
	 * @param DoctrineCollections\ArrayCollection $a
	 */
	private function partialUseWithAliasTraversableParameter(DoctrineCollections\ArrayCollection $a): void
	{

	}

	/**
	 * @return DoctrineCollections\ArrayCollection
	 */
	public function returnpartialUseWithAliasTraversable(): DoctrineCollections\ArrayCollection
	{
		return new DoctrineCollections\ArrayCollection();
	}

	/**
	 * @param string $a
	 * @param int    $b
	 */
	public function uselessAlignedParameters(string $a, int $b): void
	{

	}

}

function returnsVoid()
{
	return;
}

function returnsNothing()
{
}

/**
 * @return void
 */
function voidAnnotation()
{

}


abstract class Foo
{

	public function __construct()
	{

	}

	public function __destruct()
	{

	}

	public function __clone()
	{

	}

	public function returnsVoid()
	{
		return;
	}

	protected function returnsNothing()
	{
	}

	/**
	 * @return void
	 */
	public abstract function voidAnnotation();

}

function () {

};

function () {
	return;
};

function (): bool {

};

function () use (& $foo): \Foo\Bar {

};
