<?php

class A
{
    const LOREM = 1;
    use SomeTrait;
}

class B
{
    private static $lorem;
    private const LOREM = 1;
}

class C
{
    protected $lorem;
    private static $ipsum;
}

class D
{
    public function __construct()
    {
    }

    static function staticLorem()
    {
    }
}

class E
{
    private function __call($name, $arguments)
    {
    }

    protected static function staticDolor()
    {
    }
}

class F
{
    private function sit()
    {
    }

    private function __call($name, $arguments)
    {
    }
}

class G
{
    private function sit()
    {
    }

    protected static function staticDolor()
    {
    }
}

interface Intf
{
    static function staticLorem();

    const LOREM = 1;

    function dolor();

    public static function staticIpsum();
}

trait Tr
{
    use SomeTrait;

    static $staticLorem;

    protected static function staticDolor() {
    }

    private static $staticSit;

    private static function staticSit() {
    }

    private function __call($name, $arguments)
    {
    }

    private $sit;

    private function sit() {
    }

    public static function staticConstructor() : self
    {
    }

    public function __construct()
    {
    }

    function lorem() {
    }
}
