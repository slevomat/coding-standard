<?php

/** Class comment */
#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]
#[Attribute4(), Attribute5]
class Whatever
{

	/**
	 * Method comment
	 */
	#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]
	#[Attribute4(), Attribute5]
	public function method(
		#[Attribute1]
		$parameter
	)
	{
	}

}
