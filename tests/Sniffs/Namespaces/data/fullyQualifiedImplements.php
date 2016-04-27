<?php

class Foo implements \SomeClass, \Some\OtherClass
{

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
