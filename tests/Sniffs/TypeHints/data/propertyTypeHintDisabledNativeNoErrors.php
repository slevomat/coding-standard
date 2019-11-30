<?php

$variable = null;

class Whatever
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
	 */
	private $isSniffSuppressed;

	/**
	 * {@inheritdoc}
	 */
	private $hasInheritdocAnnotation;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	 */
	private $isSniffCodeAnyTypeHintSuppressed;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
	 * @var array
	 */
	private $isSniffCodeMissingTravesableTypeHintSpecificationSuppressed;

	/**
	 * @var int
	 */
	private $noTraversableType;

	/**
	 * @var int[]
	 */
	private $withTraversableTypeHintSpecification;

}
