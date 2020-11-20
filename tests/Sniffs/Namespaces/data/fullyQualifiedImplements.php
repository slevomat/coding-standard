<?php

class Foo implements \SomeClass, \Some\OtherClass
{
	private $version = \PHP_VERSION;
}

class Ipsum implements /*\Omega, */ Dolor
{

}

class Lorem implements \Dolor, \Amet
{

}

class LoremConsecteur implements Dolor, \Amet
{

}

class Bar implements \Dolor, Amet, Omega
{

}
