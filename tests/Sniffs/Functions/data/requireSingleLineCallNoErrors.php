<?php

class Whatever
{
	public function __construct()
	{
		$this->doAnything('true', false);

		$this->doAnything(
			'true',
			false,
			'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter'
		);

		$this->doAnything([
			true,
			false
		]);

		sprintf(
			'%s',
			'something' // Inline comment
		);

		self::doNothing([
			true,
			false,
		]);

		self::doWhatever(
			self::doAnything(true, false)
		);
	}
}
