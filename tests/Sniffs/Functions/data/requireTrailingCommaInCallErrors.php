<?php // lint >= 7.3

max(
	0,
	1
);

$closure = function () {
};

$closure(
	'something'
);

(function () {

})(
	0,
	1
);

class Whatever extends Something
{

	public function __construct($something, $anything)
	{
		parent::__construct(
			$something,
			$anything
		);

		self::doSelf(
			$something,
			$anything
		);

		static::doStatic(
			$something,
			$anything
		);

		if (isset(
			$something,
			$anything
		)) {
			unset(
				$something,
				$anything
			);
		}
	}

}

$whatever = new Whatever(
	'something',
	'anything'
);

doSomething(
	1,
	2
)(
	3,
	4
);

class SelfStaticParent extends Whatever
{

	public static function createSelf($a, $b)
	{
		return new self(
			$a,
			$b
		);
	}

	public static function createStatic($a, $b)
	{
		return new static(
			$a,
			$b
		);
	}

	public static function createParent($a, $b)
	{
		return new parent(
			$a,
			$b
		);
	}

}

call(
	function () {
	}
);

call(<<<EOM
This command will execute 'npm run' with a specified task.

Example:
 - code:npm --build-all  will build the client resources for the core, the project zed and the project yves code
EOM
);

call(
    OPTION_TASK_BUILD_YVES,
    OPTION_TASK_BUILD_YVES_SHORT,
    InputOption::VALUE_NONE,
    'execute \'npm run\' to build the project resources of yves'
);
