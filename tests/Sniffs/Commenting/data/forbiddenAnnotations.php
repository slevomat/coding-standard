<?php

/**
 * @author Slevomat
 * @see https://www.slevomat.cz
 * @Route("/", name="homepage")
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
	 * @see multiLine()
	 * @throws \Exception
	 */
	public function get(): int
	{
		return 0;
	}

	/**
	 *
	 * @throws \Throwable Text text text text
	 * text text text text text
	 * @throws \TypeError Text text
	 * text text text
	 *
	 */
	public function multiLine()
	{

	}

	/**
	 * Description description description
	 *  description description
	 *
	 * @see https://www.slevomat.cz
	 */
	public function forbiddenAnnotationAtTheEnd()
	{

	}

	/**
	 * @see https://www.slevomat.cz
	 *
	 * @return Description
	 */
	public function forbiddenAnnotationAtTheBeginning()
	{

	}

	/**
	 * Description description description
	 *  description description
	 *
	 * @see https://www.slevomat.cz
	 *
	 * @return Description
	 */
	public function withEmptyLinesAroundForbiddenAnnotation()
	{

	}

	/** @see https://www.slevomat.cz */
	public function oneLineAnnotation()
	{
	}

}
