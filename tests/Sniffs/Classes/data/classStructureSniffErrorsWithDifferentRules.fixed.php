<?php

class A
{
    use SomeTrait;
    const LOREM = 1;
}

class B
{
    private const LOREM = 1;
    private static $lorem;
}

class C
{
    private static $ipsum;
    protected $lorem;
}

class D
{
    static function staticLorem()
    {
    }

    public function __construct()
    {
    }
}

class E
{
    protected static function staticDolor()
    {
    }

    private function __call($name, $arguments)
    {
    }
}

class F
{
    private function __call($name, $arguments)
    {
    }

    private function sit()
    {
    }
}

class G
{
    protected static function staticDolor()
    {
    }

    private function sit()
    {
    }
}

interface Intf
{
    const LOREM = 1;

    static function staticLorem();

    public static function staticIpsum();

    function dolor();
}

trait Tr
{
    use SomeTrait;

    static $staticLorem;

    private static $staticSit;

    protected static function staticDolor() {
    }

    private static function staticSit() {
    }

    private $sit;

    private function __call($name, $arguments)
    {
    }

    private function sit() {
    }

    function lorem() {
    }

    public function __construct()
    {
    }

    public static function staticConstructor() : self
    {
    }
}
