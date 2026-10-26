<?php // lint >= 8.3

class Whatever
{

    final public const int A = 1;
    final public const int B = 2;

    public final const ?string C = null;
    public final const ?string D = 'd';

    protected const int|string E = 1;
    protected const int|string F = 'f';

    /**
     * @var bool
     */
    private const bool G = true;
    /**
     * @var bool
     */
    private const bool H = false;

    final const I = 'i';
    final const J = 'j';

}
