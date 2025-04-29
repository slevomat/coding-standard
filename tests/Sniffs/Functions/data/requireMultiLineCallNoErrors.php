<?php // lint >= 7.4

class Whatever
{
	public function foo()
	{
		$this->doSomething();

		$this->doAnything(
			'true',
			false
		);

		if (sprintf(
			'%s',
			'something',
		)) {

		}

		$this->doNothing('short parameter');

		return new self(
			true,
		);
	}
}

printf(
	'%s',
	'something'
);

$array = array_merge([], array_map(function (): string {
	return 'very looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong value';
}, []));

$array = array_map(['a loooooooooooooooooong value that exactly fits into the line length limit'], function (): string {
	return 'foo';
});
