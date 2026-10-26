<?php // lint >= 8.3

class Whatever
{

    final public const int A = 1, B = 2;

    public final const ?string C = null, D = 'd';

    protected const int|string E = 1,
        F = 'f';

    /**
     * @var bool
     */
    private const bool G = true, H = false;

    final const I = 'i', J = 'j';

}
