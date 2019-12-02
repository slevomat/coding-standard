<?php // lint >= 7.4

class Whatever
{

	private $noTypeHintNoAnnotation;

	/**
	 * @var int[]
	 */
	public array $arrayTypeHint;

	/**
	 * @var int[]|null
	 */
	public ?array $arrayTypeHintWithNull;

	/**
	 * @var array{foo: int}
	 */
	public array $arrayShapeTypeHint;

	public ?string $twoTypeWithNull;

	/**
	 * @var int[]|\Traversable
	 */
	public \Traversable $specificTraversable;

	public string $uselessDocComment;

	public array $missingAnnotationForTraversable;

	/**
	 * @var array
	 */
	public array $missingItemsSpecification;

	/**
	 * @var \Closure(): array
	 */
	public \Closure $callable;

	/**
	 * @var \Traversable
	 */
	public \Traversable $onlyTraversable;

	/**
	 * @var array{array}
	 */
	public array $arrayShapeWithoutItemsSpecification;

	/**
	 * @var \Generic<array>
	 */
	public \Generic $genericWithoutItemsSpecification;

	/**
	 * @var array[]&\Traversable
	 */
	public \Traversable $traversableIntersection;

	/**
	 * @var \Traversable&array[]
	 */
	public \Traversable $traversableIntersectionDifferentOrder;

	/**
	 * @var null|\Traversable
	 */
	public ?\Traversable $traversableNull;

	public object $object;

	/**
	 * Description.
	 *
	 */
	public string $uselessAnnotationWithDescription;

	/**
	 * @see Anything
	 */
	public string $uselessAnnotationWithOtherAnnotation;

	static public int $staticFirst;

	public static int $staticSecond;

}
