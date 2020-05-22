<?php // lint >= 7.4

class ClassWithTypedProperties
{

	private self $self;
	private ?self $empty;
	private ClassWithTypedProperties $static;
	private \ClassWithTypedProperties $static2;
	private int $int;

	public function __construct()
	{
		$this->self = $this;
		$this->empty = null;
		$this->static = $this;
		$this->static2 = $this;
		$this->int = 0;
	}

	public function getSelf(): self
	{
		return $this->self;
	}

}
