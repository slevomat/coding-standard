<?php // lint >= 7.4

class Whatever
{

	private $noTypeHintNoAnnotation;

	/**
	 * @var int[]
	 */
	public $arrayTypeHint;

	/**
	 * @var int[]|null
	 */
	public $arrayTypeHintWithNull;

	/**
	 * @var array{foo: int}
	 */
	public $arrayShapeTypeHint;

	/**
	 * @var string|null
	 */
	public $twoTypeWithNull;

	/**
	 * @var int[]|\Traversable
	 */
	public $specificTraversable;

	/**
	 * @var string
	 */
	public string $uselessDocComment;

	public array $missingAnnotationForTraversable;

	/**
	 * @var array
	 */
	public array $missingItemsSpecification;

	/**
	 * @var \Closure(): array
	 */
	public $callable;

	/**
	 * @var \Traversable
	 */
	public $onlyTraversable;

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
	public $traversableIntersection;

	/**
	 * @var \Traversable&array[]
	 */
	public $traversableIntersectionDifferentOrder;

	/**
	 * @var null|\Traversable
	 */
	public $traversableNull;

	/**
	 * @var object
	 */
	public $object;

	/**
	 * Description.
	 *
	 * @var string
	 */
	public string $uselessAnnotationWithDescription;

	/**
	 * @see Anything
	 * @var string
	 */
	public string $uselessAnnotationWithOtherAnnotation;

	/** @var int */
	static public $staticFirst;

	/** @var int */
	public static $staticSecond;

}
