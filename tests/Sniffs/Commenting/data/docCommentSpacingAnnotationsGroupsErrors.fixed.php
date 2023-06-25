<?php

/**
 * @author Jaroslav Hanslík
 */
class Whatever
{

	/**
	 * Description
	 *
	 * @FooAliasBar()
	 * @FooAliasBaz()
	 *
	 * @var string
	 */
	private $property;

	/**
	 * MultiLine
	 * description
	 *
	 * @param bool $a
	 *
	 * @X\Boo MultiLine
	 *    description
	 * @X\Foo(
	 *     a=Y::SOME,
	 *     b={
	 *         @Z(
	 *             code=123
	 *         )
	 *     }
	 * ) Description
	 * @XX
	 *
	 * @throws \Exception
	 */
	public function method()
	{

	}

	/**
	 * Another method.
	 *
	 * @link https://github.com/slevomat/coding-standard
	 * @link https://github.com/slevomat/coding-standard
	 * @todo Make things happen.
	 *
	 * @whatever
	 *
	 * @anything
	 *
	 * @undefined
	 * @undefined
	 */
	public function anotherMethod()
	{

	}

	/**
	 * @dataProvider oneMoreMethodData
	 *
	 * @param int $a
	 * @param int|null $b
	 * @param string $c
	 */
	public function oneMoreMethod($a, $b, $c)
	{

	}

	/**
	 * @param int $a
	 *
	 * @return bool
	 */
	public function methodBeforeInvalidDocComment($a): bool
	{

	}

	/**
	 * @param int $a
	 *
	 * @return bool
	 */
	public function methodWithInvalidDocComment($a): bool
	{

	}

	/**
	 * @first
	 * @second
	 */
	public function twoUndefinedAnnotations()
	{

	}

	/**
	 * @param int $a
	 *
	 * @phpstan-param int $a
	 * @phpstan-return bool
	 * @phpstan-whatever X
	 *
	 * @phpcs:disable
	 * @phpcs:enable
	 */
	public function phpstanAndPhpcsAnnotations($a)
	{
		return false;
	}

	/**
	 * @phpstan-return array<string, string>
	 *
	 * @return array<string, string>
*/
	public function phpstanReturnAndReturnAnnotations()
	{
		return [];
	}

}

class Doctrine
{

	/**
	 * Loads an ORM second level cache bundle mapping information.
	 *
	 * @param array<string, mixed> $entityManager A configured ORM entity manager
	 * @param Definition           $ormConfigDef  A Definition instance
	 * @param ContainerBuilder     $container     A ContainerBuilder instance
	 *
	 * @example
	 *  entity_managers:
	 *      default:
	 *          second_level_cache:
	 *              region_lifetime: 3600
	 *              region_lock_lifetime: 60
	 *              region_cache_driver: apc
	 *              log_enabled: true
	 *              regions:
	 *                  my_service_region:
	 *                      type: service
	 *                      service : "my_service_region"
	 *
	 *                  my_query_region:
	 *                      lifetime: 300
	 *                      cache_driver: array
	 *                      type: filelock
	 *
	 *                  my_entity_region:
	 *                      lifetime: 600
	 *                      cache_driver:
	 *                          type: apc
	 */
	public function method()
	{
	}

	/**
	 * @dataProvider
	 *
	 * @param string $a First line
	 * Second line
	 *
	 * Third line
	 * Forth line
	 * @param string $b First line
	 * Second line
	 *
	 * Third line
	 * Forth line
	 *
	 * @return void
	 */
	public function multiLineAnnotations($a, $b)
	{
	}

}
