<?php

/**
 *
 * @author Jaroslav Hanslík
 */

/**
 * Description
 *
 */
class Whatever
{

	/**
	 * Description
	 *
	 *
	 * @var string
	 */
	private $property;

	/**
	 *
	 *
	 * MultiLine
	 * description
	 *
	 *
	 * @param bool $a
	 * @X(
	 *     a=Y::SOME,
	 *     b={
	 *         @Z(
	 *             code=123
	 *         )
	 *     }
	 * ) Description
	 * @X MultiLine
	 *    description
	 *
	 * @throws \Exception
	 *
	 *
	 */
	public function method()
	{

	}

	/**
	 * Another method.
	 *
	 * @link https://github.com/slevomat/coding-standard
	 *
	 * @todo Make things happen.
	 *
	 * @link https://github.com/slevomat/coding-standard
	 */
	public function anotherMethod()
	{

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
	 *
	 *
	 * @phpcs:enable
	 */
	public function method()
	{
	}

}
