<?php // lint >= 8.0

class Whatever
{

	private string|int $withTypeHint;

	/** @var string|true */
	private string|bool $withTrue;

	/**
	 * @var ArrayHash|array|mixed[]
	 */
	protected $arrayAndArrayHash;

}
