<?php

class Whatever
{

	public function beforeAnyAnonymousClass(): Whatever
	{
		return new class {

			public function insideFirst(): Whatever
			{
			}

		};
	}

	public function afterClosedAnonymousClass(): Whatever
	{
		return new class extends Anything {

			public function outer(): Whatever
			{
				$nested = new class {

					public function insideNested(): Whatever
					{
					}

				};

				return new Whatever();
			}

		};
	}

	public function extendsAfterClosedAnonymousClass()
	{
		return new class extends Whatever {

		};
	}

	public function afterAllAnonymousClasses(Whatever $whatever): Whatever
	{
		return $whatever;
	}

}
