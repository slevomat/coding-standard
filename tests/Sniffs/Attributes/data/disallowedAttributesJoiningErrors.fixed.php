<?php // lint >= 8.1

#[Attribute1] #[Attribute2('var')]
	#[Attribute3(option: PDO::class, option2: true, option3: 'False')]#[Attribute4()]#[Attribute5]
class TestClass
{
	#[Attribute1] #[Attribute2('var')]
		#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
	private const FOO = 'foo';

	#[Attribute1] #[Attribute2('var')]
		#[Attribute3(option: PDO::class, option2: true, option3: 'False')] private string $foo;

	public function __construct(#[Attribute1] #[Attribute2('var')]
								#[Attribute3(option: PDO::class, option2: true, option3: 'False')] $test)
	{
	}
}

$object = new #[Attribute1] #[Attribute2('var')]
	#[Attribute3(option: PDO::class, option2: true, option3: 'False')] class () { /* … */ };

#[Attribute1] #[Attribute2('var')]
#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
function testFunc($test)
{
}

$fn = #[Attribute1] #[Attribute2('var')]
	#[Attribute3(option: PDO::class, option2: true, option3: 'False')] function() {
	return true;
};

$fn2 = #[Attribute1] #[Attribute2('var')]
	#[Attribute3(option: PDO::class, option2: true, option3: 'False')] fn() => true;

#[Attribute1] #[Attribute2('var')]
	#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
trait sampleTrait{
	function traitFunc() { }
}

#[Attribute1] #[Attribute2('var')]
	#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
interface sampleInterface{
	function traitFunc();
}

#[Attribute1] #[Attribute2('var')]
	#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
enum sampleEnum: string{
	#[Attribute1] #[Attribute2('var')]
		#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
	case sample = 'S';
}
