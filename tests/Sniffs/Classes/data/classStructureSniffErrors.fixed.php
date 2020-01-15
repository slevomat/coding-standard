<?php

class A
{
    use SomeTrait;
    const LOREM = 1;
}

class B
{
    const IPSUM = 1;
    private const LOREM = 1;
}

class C
{
    private const LOREM = 1;
    private static $lorem;
}

class D
{
    private static $ipsum;
    protected $lorem;
}

class E
{
    static function staticLorem()
    {
    }

    public function __construct()
    {
    }
}

class F
{
    protected static function staticDolor()
    {
    }

    private function __call($name, $arguments)
    {
    }
}

class G
{
    public function __construct()
    {
    }

    public function __get($name)
    {
    }
}

class H
{
    private function __call($name, $arguments)
    {
    }

    private function sit()
    {
    }
}

class I
{
    protected static function staticDolor()
    {
    }

    private function sit()
    {
    }
}

class J
{
    protected static function staticDolor()
    {
    }

    private static function staticSit()
    {
    }
}

class K
{
    function lorem()
    {
    }

    private function sit()
    {
    }
}

class L
{
    function lorem()
    {
        new class()
        {
            const IPSUM = 1;
            private const LOREM = 1;

            function ipsum()
            {
            }

            private function lorem()
            {
            }
        };
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

    private $sit;

    protected static function staticDolor() {
    }

    private static function staticSit() {
    }

    public function __construct()
    {
    }

    private function __call($name, $arguments)
    {
    }

    function lorem() {
    }

    private function sit() {
    }
}

class M
{
    public static function notAStaticConstructorPlacedCorrectly() : D
    {
    }

    public static function notAStaticConstructorA()
    {
    }

    public static function notAStaticConstructorB() : D
    {
    }

    /**
     * @return D
     */
    public static function notAStaticConstructorC()
    {
    }

    private function __construct()
    {
    }
}
