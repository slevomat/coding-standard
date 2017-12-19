<?php

namespace XXX;

use YYY\Partial;
use YYY\UsedNamespaceClass;

class Foo extends \XXX\SameNamespaceClass
{

}

class Bar implements \XXX\SameNamespaceInterface
{

}

class Faz extends \XXX\SameNamespaceClass implements \XXX\SameNamespaceInterface
{

}

class Bah extends \YYY\UsedNamespaceClass
{

}

class Bam extends \YYY\Partial\PartialyUsedClass
{

}

class Boo extends \YYY\AlreadyFqnClass
{

}

class Bee
	extends \XXX\SameNamespaceClass
	implements \XXX\SameNamespaceInterface
{

}

class Fee implements \XXX\MultipleInterface1, \XXX\MultipleInterface2
{

}
