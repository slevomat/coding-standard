<?php

class Foo
{

	use BarTrait {
		BarTrait::foo as private;
	}

	use FooTrait {
		FooTrait::bar as private;
	}

}
