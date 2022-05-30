<?php

/** Created by Slevomat. */

/**
 * This is
 * multiLine.
 */

/**
 * Class WithDocComment
 *
 * @see https://www.slevomat.cz
 */
abstract class WithDocCommentAndDescription
{

	/**
	 * Constant WITH_DOC_COMMENT_AND_DESCRIPTION
	 *
	 * @var bool
	 */
	const WITH_DOC_COMMENT_AND_DESCRIPTION = true;

	/**
	 * @var bool
	 */
	const WITH_DOC_COMMENT = true;

	const WITHOUT_DOC_COMMENT = false;

	/**
	 * Property with doc comment and description
	 *
	 * @var bool
	 */
	private $withDocCommentAndDescription;

	/**
	 * @var bool
	 */
	protected static $withDocComment;

	public $withoutDocComment;

	/**
	 * @var bool
	 */
	public $legacyWithDocComment;

	/**
	 * Function with doc comment and description
	 * And is multi-line
	 *
	 * @see Whatever
	 *
	 * And also nothing here
	 */
	final public function withDocCommentAndDescription($d)
	{

	}

	/**
	 * @see Whatever
	 */
	public static function withDocComment($b, $c)
	{

	}

	abstract public function withoutDocComment();

}

/**
 * @see https://www.zlavomat.sk
 */
interface WithDocComment
{

}

trait WithoutDocComment
{

}

/**
 */
class EmptyDocComment
{

}

/** @var InlineDocComment */
$inlineDocComment = new InlineDocComment();

/** Invalid inline doccomment */

/**
 *
 */
class PropertyDoesNotHaveDocCommentButClassHas
{

	private $propertyWithoutDocCommentInClassWithDocComment;

}

/**
 * Class with attribute.
 */
#[Attribute]
class ClassWithAttribute
{
}

/**
 * Class with attributes.
 */
#[Attribute]
#[Attribute2]
#[Something(Anything::TARGET_CLASS | \Whatever\Anything::IS_REPEATABLE, PHP_VERSION, parameter1: 123, parameter2: [Nothing::SOMETHING, 'string'])]
class ClassWithAttributes
{
}
