<?php // lint >= 8.0

#[Attribute1] #[Attribute2('var')]
#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
class TestClass
{
    public function __construct(#[Attribute1] #[Attribute2('var')]
                                #[Attribute3(option: PDO::class, option2: true, option3: 'False')] $test)
    {
    }
}

$object = new #[Attribute1] #[Attribute2('var')]
    #[Attribute3(option: PDO::class, option2: true, option3: 'False')] class () { /* … */ };

#[Attribute1] #[Attribute2('var')]
#[Attribute3(option: PDO::class, option2: true, option3: 'False')]
#[Attribute4,]
function testFunc($test)
{
}
