<?php

namespace Foo\Bar;

use Some\Other\ClassName;

class Foo
{
    /**
     * @see http://www.loremIpsumDolorSitAmetConsecteturAdipiscingElitBonumIncolumis.cz
     * and @see http://www.aciesMiseraCaecitasMateriamVeroRerumEtCopiamApudHosExilemApud.cz
     */
    public function someMethodNameHere() : string
    {
        // http://www.loremIpsumDolorSitAmetConsecteturAdipiscingElitBonumIncolumisAciesMiseraCaecitasMateriamVeroRerumEtCopiamApudHosExilemApud.cz
        return 'string that fits the limit';
    }
}

$foo = new Foo(); // inline comment here
echo $foo->someMethodNameHere();
