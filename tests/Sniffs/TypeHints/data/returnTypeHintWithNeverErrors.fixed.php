<?php // lint >= 8.1

class Whatever
{

	/** */
	public function returnsNever(): never
	{}

	/**
	 */
	public function returnsNoReturn(): never
	{}

	/**
	 */
	public function hasVoidAndCanHaveNever(): never
	{}

	/**
	 */
	public function hasVoidAndCanHaveNever2(): never
	{}

}
