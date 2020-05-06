<?php

/**
 * @see https://www.slevomat.cz
 */
abstract class WithAnnotation
{

	/**
	 * @var bool
	 */
	const WITH_ANNOTATION = true;

	const WITHOUT_ANNOTATION = false;

	/**
	 * @var null|int|float
	 */
	protected static $withAnnotation = 1;

	public $withoutAnnotation;

}

abstract class WithoutAnnotation
{

	/**
	 * @param string $a
	 * @param int|null $b
	 */
	abstract public function withAnnotation($b, $c);

	/**
	 * @Route("/", name="homepage")
	 */
	abstract public function withParametrizedAnnotation();

	/**
	 * @Security("is_granted('ROLE_ADMIN')")
	 */
	abstract public function withParametrizedAnnotationContainingParenthesis();

	/**
	 * @Assert\Callback()
	 */
	abstract public function withParametrizedAnnotationWithoutParameters();

	/**
	 * Without annotation
	 */
	abstract public function withoutAnnotation();

	/**
	 * @Route("/configs/{config}/domains/{domain}/locales/{locale}/messages", name="jms_translation_update_message",
	 *     defaults = {"id" = null}, options = {"i18n" = false}, methods={"PUT"})
	 */
	abstract public function withMultiLineParametrizedAnnotation();

	/** @ORM\OneToMany(targetEntity=Bar::class, mappedBy="boo") */
	private $inlineDocComment;

	/**
	 * @X(
	 *     a=Y::SOME,
	 *     b={
	 *         @Z(
	 *             code=123
	 *         )
	 *     }
	 * ) Content
	 */
	private $multiLineIndentedAnnotation;

	/**
	 * @property-read Foo $propertyRead Description
	 */
	private $annotationWithDash;

	/**
	 * @return string|null
	 */
	public function withReturnAnnotation()
	{

	}

	/**
	 * @param array $parameters {
	 *     Optional. Parameters for filtering the list of user assignments. Default empty array.
	 *
	 *     @type bool $is_active                Pass `true` to only return active user assignments and `false` to
	 *                                          return  inactive user assignments.
	 *     @type DateTime|string $updated_since Only return user assignments that have been updated since the given
	 *                                          date and time.
	 * }
	 */
	public function wordPress(array $parameters = [])
	{
	}

}
