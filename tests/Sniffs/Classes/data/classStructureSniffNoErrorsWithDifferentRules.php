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
    private $ipsum;
    protected $lorem;
}

class D
{
    use SomeTrait;

    const LOREM = 1;
    private const SIT = 1;
    protected const DOLOR = 1;
    public const IPSUM = 1;

    static $staticLorem;
    private static $staticSit;
    protected static $staticDolor;
    public static $staticIpsum;

    static function staticLorem() {
    }

    private static function staticSit() {
    }

    protected static function staticDolor() {
    }

    public static function staticIpsum() {
    }

    var $lorem;
    private $sit;
    protected $dolor;
    public $ipsum;

    private function __call($name, $arguments)
    {
    }

    public function __get($name)
    {
    }

    function lorem() {
    }

    private function sit() {
    }

    public function ipsum() {
    }

    protected function dolor() {
    }

    public function __construct()
    {
    }

    public function __destruct()
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
