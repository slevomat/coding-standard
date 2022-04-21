<?php // lint >= 8.1

class Whatever
{

	/** @return never */
	public function returnsNever()
	{}

	/**
	 * @return no-return
	 */
	public function returnsNoReturn()
	{}

	/**
	 * @return never
	 */
	public function hasVoidAndCanHaveNever(): void
	{}

	/**
	 * @return no-return
	 */
	public function hasVoidAndCanHaveNever2(): void
	{}

}
