<?php

/**
 * This prose mentions array{
 *     value: string
 * } without declaring a type.
 * @phpstan-type Empty array{
 * }
 * @phpstan-type EmptyList list{
 * }
 * @phpstan-type AlreadyFixed array{
 *     nested: list{
 *         int,
 *     },
 * }
 */
class Example
{

	/**
	 * @phpstan-var array{value: string}|list{int}|non-empty-array{key: int}|non-empty-list{bool}
	 */
	public $singleLine;

	/**
	 * @phpstan-var object{
	 *     value: string
	 * }
	 */
	public $object;

	/**
	 * @phpstan-var array{
	 *     broken:
	 * }
	 */
	public $invalid;

	/**
	 * @var string Description mentions array{
	 *     value: int
	 * } but is not a shape type.
	 */
	public $description;

}
