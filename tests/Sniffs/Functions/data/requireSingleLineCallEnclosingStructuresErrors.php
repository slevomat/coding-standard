<?php // lint >= 8.1

doSomething(
	doAnything(
		true
	)
);

doSomething(function () {
	doAnything(
		true
	);
});

doSomething(fn () => doAnything(
	true
));

$object->doSomething(
	doAnything(
		true
	)
);

Something::doSomething(
	doAnything(
		true
	)
);

function doWithDefault($parameter = new Something(
	true
))
{
}

#[Deprecated(new Something(
	true
))]
function doWithAttribute()
{
}

if (
	doAnything(
		true
	)
) {
}

$array = array(
	doAnything(
		true
	)
);

doSomething(new class {
	public function doNothing()
	{
		doAnything(
			true
		);
	}
});

class Something
{
	public function doNothing()
	{
		doSomething(function () {
			doAnything(
				true
			);
		});

		$closure = function () {
			doAnything(
				true
			);
		};
	}
}

doSomething(new class {
	public function &doReference(
		$parameter
	) {
		return $parameter;
	}
});
