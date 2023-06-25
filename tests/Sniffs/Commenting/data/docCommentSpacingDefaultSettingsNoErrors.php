<?php

/**
 * @author Jaroslav Hanslík
 */

/**
 * Description
 */
class Whatever
{

	/**
	 * Description
	 *
	 * @var string
	 */
	private $property;

	/**
	 * MultiLine
	 * description
	 *
	 * @param bool $a
	 * @return void
	 * @throws \Exception
	 * @phpcs:disable Whatever
	 */
	public function method()
	{

	}

	/**
	 * @group GH-6362
	 *
	 * SELECT a as base, b, c, d
	 * FROM Start a
	 * LEFT JOIN a.bases b
	 * LEFT JOIN Child c WITH b.id = c.id
	 * LEFT JOIN c.joins d
	 */
	public function descriptionAfterAnnotation()
	{
	}

	/**
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
	 * @return void
	 */
	public function multiLineAnnotations($a, $b)
	{
	}

}
