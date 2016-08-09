<?php

namespace External;

abstract class AbstractFooClass
{

	/**
	 * @param string $cacheKey
	 */
	abstract public function stringParameterWithoutHint($cacheKey);

	/**
	 * @param string|null $cacheKey
	 */
	abstract public function stringNullableParameterWithoutHint($cacheKey);

	/**
	 * @param \Traversable|string[] $cacheKey
	 */
	abstract public function stringTraversableParameterWithoutHint($cacheKey);

}
