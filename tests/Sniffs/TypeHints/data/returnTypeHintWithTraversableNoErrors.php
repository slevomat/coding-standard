<?php // lint >= 8.0

namespace FooNamespace;

/**
 * @template T
 * @template U
 */
class Generic
{
}

class Bar
{
}

class Whatever
{

    /**
     * @return Generic<Bar, $this>
     */
    public function withThisTemplateType(): Generic
    {
        return new Generic();
    }

    /**
     * @return Generic<Bar, self>
     */
    public function withSelfTemplateType(): Generic
    {
        return new Generic();
    }

    /**
     * @return Generic<Bar, static>
     */
    public function withStaticTemplateType(): Generic
    {
        return new Generic();
    }

    /**
     * @return Generic<$this, Bar>
     */
    public function withThisAsFirstParam(): Generic
    {
        return new Generic();
    }
}
