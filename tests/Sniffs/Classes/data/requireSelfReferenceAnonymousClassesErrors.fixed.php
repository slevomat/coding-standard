<?php

class Whatever
{

	public function beforeAnyAnonymousClass(): self
	{
		return new class {

			public function insideFirst(): Whatever
			{
			}

		};
	}

	public function afterClosedAnonymousClass(): self
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

	public function afterAllAnonymousClasses(self $whatever): self
	{
		return $whatever;
	}

}
