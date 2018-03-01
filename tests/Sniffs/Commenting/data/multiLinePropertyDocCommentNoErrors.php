<?php

class Foo
{

	public $noCommentPublic;

	protected $noCommentProtected;

	private $noCommentPrivate;

	var $noCommentVar;

	/**
	 * @var int
	 */
	public $alreadyOneLinePublic;

	/**
	 * @var int
	 */
	public $alreadyOneLineProtected;

	/**
	 * @var int
	 */
	public $alreadyOneLinePrivate;

	/**
	 * @var int
	 */
	public $alreadyOneLineVar;

	/**
	 * @var int
	 */
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

	/**
	 * @var int @deprecated @internal
	 */
	public $withMultipleInlineAnnotations;


	/**
	 * @internal
	 */
	public const FOO = 1;

	/**
	 * @internal
	 */
	public function a()
	{
		/** Foo */
		$foo = true;

		/**
		 * Bar
		 */
		$bar = false;
	}

}
