<?php // lint >= 8.0

/** Class comment */
#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]

#[Attribute4(), Attribute5]
#[Attribute6] class Whatever
{

	#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]
	#[Attribute4(), Attribute5]


	/**
	 * Method comment
	 */
	#[Attribute6] public function method(
		#[Attribute1]



		$parameter1,
		#[Attribute2] $parameter2
	)
	{
	}

}
