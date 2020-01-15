<?php

class A
{
    public function singleLine(int $abc, string $efg) : void
    {
    }

    public function singleLineWithNoReturnType(int $abc, string $efg)
    {
    }

    public function multiLine(
        \DateTimeImmutable $someLongNameHere,
        \DateTimeImmutable $andAnotherLongNameOverThere
    ) : void {
    }

    public function multiLineWithNoReturnType(
        \DateTimeImmutable $someLongNameHere,
        \DateTimeImmutable $andAnotherLongNameOverThere
    ) {
    }

    public function multiLineMethodWithPrecisely121CharsOnSingleline(
        $someHugeVariableNameJustToFillTheSpaceBlaah
    ) : void {
    }
}

interface B
{
    public function singleLine(int $abc, string $efg) : void;

    public function singleLineWithNoReturnType(int $abc, string $efg);

    public function multiLine(
        \DateTimeImmutable $someLongNameHere,
        \DateTimeImmutable $andAnotherLongNameOverThere
    ) : void;

    public function multiLineWithNoReturnType(
        \DateTimeImmutable $someLongNameHere,
        \DateTimeImmutable $andAnotherLongNameOverThere
    );

    public function multiLineMethodWithPrecisely121CharsOnSingleline(
        $someHugeVariableNameJustToFillTheSpaceBlah
    ) : void;
}

function thisSniffOnlyAppliesToMethodsSoFunctionShouldBeIgnored(
    \DateTimeImmutable $someLongNameHere,
    \DateTimeImmutable $andAnotherLongNameOverThere
) : void {
}
