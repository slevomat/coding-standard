<?php

namespace XXX;

use YYY\Partial;
use YYY\UsedNamespaceClass;

class Foo extends SameNamespaceClass
{

}

class Bar implements SameNamespaceInterface
{

}

class Faz extends SameNamespaceClass implements SameNamespaceInterface
{

}

class Bah extends UsedNamespaceClass
{

}

class Bam extends Partial\PartialyUsedClass
{

}

class Boo extends \YYY\AlreadyFqnClass
{

}

class Bee
	extends SameNamespaceClass
	implements SameNamespaceInterface
{

}

class Fee implements MultipleInterface1, MultipleInterface2
{

}
