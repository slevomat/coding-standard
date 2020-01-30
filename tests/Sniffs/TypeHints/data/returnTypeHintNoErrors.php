<?php

class Whatever
{

	public function __construct()
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint
	 */
	private function isSniffSuppressed()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	private function hasInheritdocAnnotation()
	{
		return true;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
	 */
	private function isSniffCodeAnyTypeHintSuppressed()
	{
		return true;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 * @return int
	 */
	private function isSniffCodeMissingNativeTypeHintSuppressed()
	{
		return 0;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
	 * @return array
	 */
	private function isSniffCodeMissingTravesableTypeHintSpecificationSuppressed(): array
	{
		return [];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.UselessAnnotation
	 * @return int
	 */
	private function isSniffCodeUselessAnnotationSuppressed(): int
	{
		return 0;
	}

	private function noTraversableType(): int
	{
		return 0;
	}

	/**
	 * @return int[]
	 */
	private function withTraversableTypeHintSpecification(): array
	{
		return [];
	}

	/**
	 * @return null
	 */
	public function nullReturnValue()
	{
		return null;
	}

	/**
	 * @return ?string
	 */
	public function shorthandNullableDocblock(): ?string
	{
		return rand(0, 1) ? null : '';
	}

	/**
	 * @return string|int|bool
	 */
	public function aLotOfTypes()
	{
		return 0;
	}

	/**
	 * @return string|int
	 */
	public function twoTypeNoNullOrTraversable()
	{
		return 0;
	}

	/**
	 * @return mixed
	 */
	public function invalidType()
	{
		return 0;
	}

	/**
	 * @return int[]|\DateTimeImmutable
	 */
	public function twoTypesNoTraversable()
	{
		return [];
	}

	/**
	 * @return \Boo<bool>|\Foo
	 */
	public function generic()
	{
		return new \Boo();
	}

	/**
	 * @return
	 */
	public function emptyAnnotation(): int
	{
		return 0;
	}

	/**
	 * @return $this
	 */
	public function containsThis(): self
	{
		return $this;
	}

	/**
	 * @return $this|null
	 */
	public function containsThisOrNull(): ?self
	{
		return $this;
	}

	public function closureThatReturnsSomething(): \Closure
	{
		return function () {
			return true;
		};
	}

	public function closureWithTypeHint(): \Closure
	{
		return function (): void {
		};
	}

	/**
	 * @return $this
	 */
	public function returnsThis()
	{
		return $this;
	}

	/**
	 * @return array<string, callable(mixed $value) : string>
	 */
	public function callableArray(): array
	{

	}

	/**
	 * @return Whatever|Something|Anything
	 */
	public function unionWithDifferentBase()
	{
	}

	/**
	 * @return array<int>|array<bool>|(A&B)
	 */
	public function unionWithMoreDifferentBase()
	{
	}

	/**
	 * @return Whatever|Something|Anything|null
	 */
	public function unionWithDifferentNullableBase($a)
	{
	}

}
