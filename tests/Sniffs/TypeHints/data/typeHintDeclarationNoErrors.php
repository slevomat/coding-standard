<?php

namespace FooNamespace;

use Doctrine;
use Doctrine\Common\Collections as DoctrineCollections;
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
	public function parametersTypeHintsSuppressed($a, $b): void
	{
	}

	public function noParameters(): void
	{
	}

	public function allParametersWithTypeHint(string $a, int $b): void
	{
	}

	/**
	 * @param mixed $a
	 */
	public function mixedParameter($a): void
	{
	}

	/**
	 * @param mixed[]|array $a
	 */
	public function mixedTraversableParameter(array $a): void
	{
	}

	/**
	 * @param string|integer $a
	 */
	public function moreTypesParameter($a): void
	{
	}

	/**
	 * @param null $a
	 */
	public function nullParameter($a): void
	{
	}

	/**
	 * @param string[] $a
	 */
	public function arrayParameter(array $a): void
	{
	}

	/**
	 * @param string[] $a
	 */
	public function traversableParameter(\Traversable $a): void
	{
	}

	/**
	 * @param \FooClass[]|\QueryResultSet $a
	 */
	public function moreTypeWithTraversableParameter(\QueryResultSet $a): void
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

	abstract function abstractFunction(): void;

	public function withoutReturn(): void
	{
		// Nothing
	}

	public function returnsVoid(): void
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
	public function staticAsSelf(self $a): void
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
	public function uselessCommentSuppressed(string $a, string $b): void
	{
	}

	public function noDocComment(): void
	{
	}


	/**
	 * Description
	 *
	 * @param string $a
	 * @param string $b
	 */
	public function withDocCommentDescription(string $a, string $b): void
	{
	}

	/**
	 * @see https://www.slevomat.cz
	 * @param string $a
	 * @param string $b
	 */
	public function withUsefulAnnotation(string $a, string $b): void
	{
	}

	/**
	 * @Assert\Callback()
	 * @param string $a
	 * @param string $b
	 */
	public function withUsefulComplexAnnotation(string $a, string $b): void
	{
	}

	/**
	 * @Something\Whatever()
	 * @param string $a
	 * @param string $b
	 */
	public function withUsefulComplexAnnotationOnlyPrefixed(string $a, string $b): void
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
	public function withAnySuppress(string $s): void
	{
	}

	/**
	 * @return FooClass|\PHPUnit\Framework\MockObject\MockObject
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
	public function returnsNullableArrayOfStrings(): ?array
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
	public function parameterResource($a): void
	{

	}

	/**
	 * @param true $a
	 */
	public function parameterTrue($a): void
	{

	}

	/**
	 * @param mixed|null $a
	 */
	public function nullableMixed($a): void
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
	public function traversableArrayParameter(array $a): void
	{
	}

	/**
	 * @param iterable|string[] $a
	 */
	private function taversableIterableParameter(iterable $a): void
	{
	}

	/**
	 * @param string[]|\Traversable $a
	 */
	abstract public function traversableTraversableParameter(\Traversable $a): void;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration
	 * @param \Traversable $a
	 */
	abstract public function traversableTraversableParameterWithGlobalSuppress(\Traversable $a): void;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableParameterTypeHintSpecification
	 * @param \Traversable $a
	 */
	abstract public function traversableTraversableParameterWithSuppress(\Traversable $a): void;

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
	public function parametersWithoutTypeHintAndWithAnnotationWithoutParameterName($a, \Traversable $b): void
	{
	}

	/**
	 * @param string $b
	 * @param \Traversable|string[]
	 * @param float $c
	 */
	public function oneParameterWithoutTypeHintAndWithAnnotationWithoutParameterName(string $a, \Traversable $b, float $c): void
	{
	}

	/**
	 * @param string $a A
	 */
	public function withParameterDescriptionWithParameterNameInAnnotation(string $a): void
	{
	}

	/**
	 * @param string ...$a A
	 */
	public function varadicWithParameterDescriptionWithParameterNameInAnnotation(string ...$a): void
	{
	}

	/**
	 * @param string A
	 */
	public function withParameterDescriptionWithoutParameterNameInAnnotation(string $a): void
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
	public function withMoreSpecificParameterAnnotation(\Something $a): void
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
	private function multidimensionalArrayParameter(array $a): void
	{
	}

	/**
	 * @param \Traversable[]|mixed[][] $a
	 */
	private function multidimensionalTraversable(array $a): void
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
	private function nullableMultidimensionalArrayParameter($a): void
	{
	}

	/**
	 * @param
	 * @return
	 */
	abstract public function invalidAnnotations(): void;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableParameterTypeHintSpecification
	 * @param string|array $a
	 */
	public function mixedContainingTraversable($a): void
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
	 * @var Doctrine\Common\Collections\ArrayCollection|mixed[]
	 */
	protected $partialUseTraversable;

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection|mixed[] $a
	 */
	private function partialUseTraversableParameter(Doctrine\Common\Collections\ArrayCollection $a): void
	{

	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|mixed[]
	 */
	public function returnpartialUseTraversable(): Doctrine\Common\Collections\ArrayCollection
	{
		return new Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @var DoctrineCollections\ArrayCollection|mixed[]
	 */
	protected $partialUseWithAliasTraversable;

	/**
	 * @param DoctrineCollections\ArrayCollection|mixed[] $a
	 */
	private function partialUseWithAliasTraversableParameter(DoctrineCollections\ArrayCollection $a): void
	{

	}

	/**
	 * @return DoctrineCollections\ArrayCollection|mixed[]
	 */
	public function returnpartialUseWithAliasTraversable(): DoctrineCollections\ArrayCollection
	{
		return new DoctrineCollections\ArrayCollection();
	}

	/**
	 * {@inheritdoc}
	 */
	public function inheritdoc($a)
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function inheritdocAnnotation($a)
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function camelizedInheritdoc($a)
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function camelizedInheritdocAnnotation($a)
	{
		return true;
	}

	/**
	 * @param bool $foo describes something about $anything
	 */
	public function parameterHasDescriptionContainingVariable(bool $foo, int $bar) : void
	{
	}

	/**
	 * @Security("is_granted('ROLE_ADMIN')")
	 */
	public function usefullSymfonySecurityAnnotation(): void
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public $inheritdoc;

}
