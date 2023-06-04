<?php

/**
 * @author Jaroslav Hanslík
 */
class Whatever
{

	/**
	 * Description
	 *
	 * @var string
	 *
	 * @FooAliasBar()
	 * @FooAliasBaz()
	 */
	private $property;

	/**
	 * MultiLine
	 * description
	 *
	 * @throws \Exception
	 * @X\Foo(
	 *     a=Y::SOME,
	 *     b={
	 *         @Z(
	 *             code=123
	 *         )
	 *     }
	 * ) Description
	 * @XX
	 * @X\Boo MultiLine
	 *    description
	 *
	 *
	 * @param bool $a
	 */
	public function method()
	{

	}

	/**
	 * Another method.
	 *
	 * @undefined
	 *
	 * @whatever
	 *
	 * @link https://github.com/slevomat/coding-standard
	 * @todo Make things happen.
	 * @link https://github.com/slevomat/coding-standard
	 *
	 * @anything
	 *
	 * @undefined
	 */
	public function anotherMethod()
	{

	}

	/**
	 * @param int $a
	 * @param int|null $b
	 * @param string $c
	 *
	 * @dataProvider oneMoreMethodData
	 */
	public function oneMoreMethod($a, $b, $c)
	{

	}

	/**
	 * @return bool
	 *
	 * @param int $a
	 */
	public function methodBeforeInvalidDocComment($a): bool
	{

	}

	/** @return bool
	 * @param int $a
	 */
	public function methodWithInvalidDocComment($a): bool
	{

	}

	/**
	 * @first
	 *
	 * @second
	 */
	public function twoUndefinedAnnotations()
	{

	}

	/**
	 * @phpstan-whatever X
	 * @phpcs:disable
	 * @phpstan-param int $a
	 * @phpcs:enable
	 * @phpstan-return bool
	 *
	 * @param int $a
	 */
	public function phpstanAndPhpcsAnnotations($a)
	{
		return false;
	}

	/** @phpstan-return array<string, string>
	* @return array<string, string> */
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
	 * @param Definition           $ormConfigDef  A Definition instance
	 * @param ContainerBuilder     $container     A ContainerBuilder instance
	 */
	public function method()
	{
	}

}
