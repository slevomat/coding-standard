<?php

class A
{
    const LOREM = 1;
    use SomeTrait;
}

class B
{
    private const LOREM = 1;
    const IPSUM = 1;
}

class C
{
    private static $lorem;
    private const LOREM = 1;
}

class D
{
    protected $lorem;
    private static $ipsum;
}

class E
{
    public function __construct()
    {
    }

    static function staticLorem()
    {
    }
}

class F
{
    private function __call($name, $arguments)
    {
    }

    protected static function staticDolor()
    {
    }
}

class G
{
    public function __get($name)
    {
    }

    public function __construct()
    {
    }
}

class H
{
    private function sit()
    {
    }

    private function __call($name, $arguments)
    {
    }
}

class I
{
    private function sit()
    {
    }

    protected static function staticDolor()
    {
    }
}

class J
{
    private static function staticSit()
    {
    }

    protected static function staticDolor()
    {
    }
}

class K
{
    private function sit()
    {
    }

    function lorem()
    {
    }
}

class L
{
    function lorem()
    {
        new class()
        {
            private const LOREM = 1;
            const IPSUM = 1;

            private function lorem()
            {
            }

            function ipsum()
            {
            }
        };
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

    private static $staticSit;
    static $staticLorem;

    private static function staticSit() {
    }

    private $sit;

    protected static function staticDolor() {
    }

    private function __call($name, $arguments)
    {
    }

    public function __construct()
    {
    }

    private function sit() {
    }

    function lorem() {
    }
}

class M
{
    public static function notAStaticConstructorPlacedCorrectly() : D
    {
    }

    private function __construct()
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
}
