<?php

#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]
#[Attribute4(), Attribute5]
/** Class comment */
class Whatever
{

	#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]
	#[Attribute4(), Attribute5]
	/**
	 * Method comment
	 */
	public function method(
		#[Attribute1] #[Attribute2] #[Attribute3]

		#[Attribute4] #[Attribute5] #[Attribute6]
		/** @param int $parameter */
		$parameter
	)
	{
	}

}
