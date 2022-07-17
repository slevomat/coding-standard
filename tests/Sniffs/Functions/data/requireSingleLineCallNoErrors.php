<?php

class Whatever
{
	public function __construct()
	{
		$this->doAnything('true', false);

		$this->doAnything(
			Nothing::class,
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

		self::doWithHeredoc(<<<'CODE'
			Anything
CODE
);

		self::doWithNewLines("
			Anything
		");
		$table = 'aaa';
		$this->query("
			INSERT INTO {$table} (id)
			VALUES (1), (2), (3)
		");
	}
}
