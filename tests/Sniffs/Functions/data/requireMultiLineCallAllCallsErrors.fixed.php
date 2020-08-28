<?php

class Whatever
{
	public function __construct()
	{
		$this->doAnything(
			'true',
			false
		);
	}
}

function ($text) {
	return sprintf(
		_(
			'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong parameter'
		),
		$text
	);
};
