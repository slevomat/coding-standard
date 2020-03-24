<?php

/**
 * @author Slevomat
 */
class Foo
{

	/**
	 * @param string $a
	 */
	public function __construct(string $a)
	{
	}

	/**
	 * @return int
	 */
	public function get(): int
	{
		return 0;
	}

	public function multiLine()
	{

	}

	/**
	 * Description description description
	 *  description description
	 */
	public function forbiddenAnnotationAtTheEnd()
	{

	}

	/**
	 * @return Description
	 */
	public function forbiddenAnnotationAtTheBeginning()
	{

	}

	/**
	 * Description description description
	 *  description description
	 *
	 * @return Description
	 */
	public function withEmptyLinesAroundForbiddenAnnotation()
	{

	}

	public function oneLineAnnotation()
	{
	}

}
