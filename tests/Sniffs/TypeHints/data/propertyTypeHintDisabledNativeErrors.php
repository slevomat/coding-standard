<?php

class Whatever
{

	/**
	 * @see Anything
	 */
	private $noVarAnnotation;

	/** @var array */
	private $arrayWithMissingTraversableTypeHintSpecification;

	/** @var iterable */
	private $iterableWithMissingTraversableTypeHintSpecification;

	/** @var \Traversable */
	private $specificTraversableWithMissingTraversableTypeHintSpecification;

	/**
	 * @var
	 */
	private $invalidVarAnnotation;

}
