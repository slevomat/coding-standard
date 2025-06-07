<?php // lint >= 8.0

#[Attribute1]
#[Attribute2('var')]
#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
#[Attribute4()]
#[Attribute5] // comment
#[Attribute6, Attribute7, Attribute8]
class TestClass
{
	public function __construct(
		#[Attribute1]
		/** comment */
		#[Attribute2('var')] /* comment */
		#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
		$test)
	{
	}
}

$object = new
#[Attribute1]
#[Attribute2('var')]
#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
class () { /* … */ };

#[Attribute1]
#[Attribute2('var')]
#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
function testFunc($test)
{
}

#[Attribute1]
function testFunc2(
	#[Attribute2('var')]
	$test)
{
}

#[Attribute1] function testFunc3(#[Attribute2('var')] $test)
{
}
