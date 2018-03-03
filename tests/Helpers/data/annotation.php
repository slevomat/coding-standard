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
	 * @var int
	 */
	protected static $withAnnotation = 1;

	public $withoutAnnotation;

}

abstract class WithoutAnnotation
{

	/**
	 * @param string $a
	 * @param string $b
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
	abstract public function withMultilineParametrizedAnnotation();

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
	private $multilineIndentedAnnotation;

	/**
	 * @var ObjectProphecy<Sample>
	 */
	private $annotationWithGeneric;

	/**
	 * @property-read Test
	 */
	private $annotationWithDash;

}
