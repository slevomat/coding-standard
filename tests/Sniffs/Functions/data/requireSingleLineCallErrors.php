<?php // lint >= 7.4

class Whatever
{
	public function foo()
	{
		$this->doAnything(
			'true',
			false
		);

		if (\sprintf(
			'%s',
			'something',
		)) {

		}

		return new self(
			true,
		);
	}
}

printf(
	'%s',
	'something'
);
