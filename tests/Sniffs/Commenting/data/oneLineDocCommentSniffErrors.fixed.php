<?php

class Foo
{

	/** @var int */
	public $withOneAnnotationOnlyPublic;

	/** @var int */
	protected $withOneAnnotationOnlyProtected;

	/** @var int */
	private $withOneAnnotationOnlyPrivate;

	/** @var int */
	var $withOneAnnotationOnlyVar;

	/** @var int */
	public $withAnnotationAndStrangeSpacingBefore;

	/** @var int */
	public $withAnnotationAndStrangeSpacingAfter;

	/** @var int */
	public $withAnnotationAndStrangeSpacingAround;

	/** @var int */
	public $withAnnotationAndNoLeadingNewline;

	/** @var int */
	public $withAnnotationAndNoTrailingNewline;

	/***/
	public $withEmptyComment;

}

/** @example foo */
class Bar
{
	/** @example foo */
	const FOO = 1;

	/** @example foo */
	public function test()
	{
		/** @var string $lorem */
		$lorem = 'lorem';

		/** @return int[] */
		$x = function () : array {
			return [1, 2, 3];
		};
	}
}
