<?php

class Foo
{

	/**
	 * @var int
	 */
	public $withOneAnnotationOnlyPublic;

	/**
	 * @var int
	 */
	protected $withOneAnnotationOnlyProtected;

	/**
	 * @var int
	 */
	private $withOneAnnotationOnlyPrivate;

	/**
	 * @var int
	 */
	var $withOneAnnotationOnlyVar;

	/**
	 * @var int
	 */
	public $withAnnotationAndStrangeSpacingBefore;

	/**
	 * @var int
	 */
	public $withAnnotationAndStrangeSpacingAfter;

	/**
	 * @var int
	 */
	public $withAnnotationAndStrangeSpacingAround;

	/**
	 * @var int
	 */
	public $withAnnotationAndNoLeadingNewline;

	/**
	 * @var int
	 */
	public $withAnnotationAndNoTrailingNewline;

	/**
	 * @var int @deprecated @internal
	 */
	public $withMultipleOneLineAnnotations;

	/**
	 *
	 */
	public $withEmptyComment;

	/**
	 *
	 */
	public $withEmptyCommentAndSpaces;

}
