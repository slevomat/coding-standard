<?php

class Foo
{

	public $noCommentPublic;

	protected $noCommentProtected;

	private $noCommentPrivate;

	var $noCommentVar;

	/** @var int */
	public $alreadyOneLinePublic;

	/** @var int */
	public $alreadyOneLineProtected;

	/** @var int */
	public $alreadyOneLinePrivate;

	/** @var int */
	public $alreadyOneLineVar;

	/** @var int */
	public $withVarOnlyOldStyle;

	/**
	 * Foo
	 * @var int
	 */
	public $withAnnotationAndDescription;

	/**
	 * Foo
	 *
	 * @var int
	 */
	public $withAnnotationAndSeparatedDescription;

	/**
	 * Foo
	 */
	public $withDescriptionOnly;

	/**
	 * @internal
	 * @var foo
	 */
	public $withMultipleAnnotations;

	/**
	 * @var int
	 * Foo
	 */
	public $withAnnotationAndTextAfter;

	/** @var @var int @deprecated @internal */
	public $withMultipleInlineAnnotations;

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
