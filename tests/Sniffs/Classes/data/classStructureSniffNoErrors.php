<?php

class A
{
}

class B
{
    const LOREM = 1;
}

class C
{
    protected $lorem;
    private $ipsum;
}

class D
{
    use SomeTrait;

    const LOREM = 1;
    public const IPSUM = 1;
    protected const DOLOR = 1;
    private const SIT = 1;

    static $staticLorem;
    public static $staticIpsum;
    protected static $staticDolor;
    private static $staticSit;

    var $lorem;
    public $ipsum;
    protected $dolor;
    private $sit;

    static function staticLorem() {
    }

    public static function staticIpsum() {
    }

    protected static function staticDolor() {
    }

    private static function staticSit() {
    }

    private function __construct()
    {
    }

    public static function staticConstructorA() : self
    {
    }

    public static function staticConstructorB() : D
    {
    }

    /**
     * @return static
     */
    public static function staticConstructorC()
    {
    }

    function __destruct()
    {
        new class ()
        {
            use SomeTrait;

            const LOREM = 1;
            public const IPSUM = 1;
            protected const DOLOR = 1;
            private const SIT = 1;

            static $staticLorem;
            public static $staticIpsum;
            protected static $staticDolor;
            private static $staticSit;

            var $lorem;
            public $ipsum;
            protected $dolor;
            private $sit;

            static function staticLorem() {
            }

            public static function staticIpsum() {
            }

            protected static function staticDolor() {
            }

            private static function staticSit() {
            }

            private function __construct()
            {
            }

            public static function staticConstructorA() : self
            {
            }

            /**
             * @return static
             */
            public static function staticConstructorB()
            {
            }

            function __destruct()
            {

            }

            private function __call($name, $arguments)
            {
            }

            public function __get($name)
            {
            }

            function lorem() {
            }

            public function ipsum() {
            }

            protected function dolor() {
            }

            private function sit() {
            }
        };
    }

    private function __call($name, $arguments)
    {
    }

    public function __get($name)
    {
    }

    function lorem() {
    }

    public function ipsum() {
    }

    protected function dolor() {
    }

    private function sit() {
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
