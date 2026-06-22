<?php // lint >= 8.2

class ConstructorPointerBranches
{
    public function process(): void
    {
        $anonymous = new class {
            public function __construct(private readonly int $id)
            {
            }
        };
    }
}

class ConstructorPointerBranchesWithAnonClassBodyProperty
{
    public function process(): void
    {
        $anonymous = new class {
            private readonly int $id;

            public function __construct(int $id)
            {
                $this->id = $id;
            }
        };
    }
}
