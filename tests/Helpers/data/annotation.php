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

interface WithoutAnnotation
{

	/**
	 * @param string $a
	 * @param string $b
	 */
	public function withAnnotation($b, $c);

	/**
	 * @Route("/", name="homepage")
	 */
	public function withParametrizedAnnotation();

	/**
	 * @Assert\Callback()
	 */
	public function withParametrizedAnnotationWithoutParameters();

	/**
	 * Without annotation
	 */
	public function withoutAnnotation();

	/**
	 * @Route("/configs/{config}/domains/{domain}/locales/{locale}/messages", name="jms_translation_update_message",
	 *     defaults = {"id" = null}, options = {"i18n" = false}, methods={"PUT"})
	 */
	public function withMultilineParametrizedAnnotation();
}
