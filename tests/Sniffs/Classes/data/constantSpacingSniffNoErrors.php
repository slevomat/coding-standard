<?php

abstract class Foo {
    public const Foo = 'bar';
    const Bar = 'foo';

    public static abstract function wow();
    private function such()
    {
    }
}

abstract class Bar {
    /** @var string */
    public const Foo = 'bar';

    /** @var string */
    const Bar = 'foo';

    /**
     * whatever
     */
    public static abstract function wow();
    /**
     * who cares
     */
    private function such()
    {
    }
}

class Foobar {
    private const ARR = [
        1,
        2,
        3,
    ];

    private const FOO = 3;
}
