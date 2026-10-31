<?php

/**
 * @phpstan-type Shared array{
 *     nested: array{
 *         value: string,},}
 * @psalm-type Tuple list{
 *     int,
 *     string,}
 */
class Example
{

	/**
	 * @phpstan-var non-empty-array{
	 *     'quoted,key'?: 'literal,}',
	 *     last: int, // Keep this comment exactly.
	 * }
	 */
	public $map;

	/**
	 * @phpstan-var non-empty-list{
	 *     string,
	 * }
	 */
	public $tuple;

	/**
	 * @phpstan-param Collection<array{
	 *     value: int,
	 * }>|null $values Preserve the description.
	 * @phpstan-return callable(array{
	 *     input: string,
	 * }): object{result: array{
	 *     output: bool,
	 * }}
	 */
	public function transform($values)
	{
	}

	/**
	 * @phpstan-var (array{
	 *     key: int,
	 * })
	 */
	public $parenthesized;

	/**
	 * @phpstan-var array{
	 *     known: int,
	 *     ...,
	 * }
	 */
	public $unsealed;

	/**
	 * @phpstan-var array{
	 *     ...<string, array{
	 *         value: int,
	 *     }>,
	 * }
	 */
	public $typedUnsealed;

}
