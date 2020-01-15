<?php

class A
{
    public function someMethod(int $abc, string $efg) : void
    {
    }

    public function someMethodWithNoReturnType(int $abc, string $efg)
    {
    }
}

interface B
{
    public function someMethod(int $abc, string $efg) : void;

    public function someMethodWithNoReturnType(int $abc, string $efg);
}
