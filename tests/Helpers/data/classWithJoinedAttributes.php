<?php // lint >= 8.0

#[Attribute1, Attribute2('var'),
    Attribute3(option: PDO::class, option2: true, option3: 'False'),Attribute4(),Attribute5]
class FooClass
{
}
