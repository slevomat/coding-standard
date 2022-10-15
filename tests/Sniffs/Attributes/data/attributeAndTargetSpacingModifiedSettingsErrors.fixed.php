<?php

/** Class comment */
#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]

#[Attribute4(), Attribute5]

class Whatever
{

	#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]
	#[Attribute4(), Attribute5]

	/**
	 * Method comment
	 */
	public function method(
		#[Attribute1]

		$parameter
	)
	{
	}

}
