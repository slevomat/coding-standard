<?php // lint >= 7.4

class ClassWithTypedProperties
{

	private self $self;
	private ?self $empty;
	private ClassWithTypedProperties $static;
	private int $int;

	public function __construct()
	{
		$this->self = $this;
		$this->empty = null;
		$this->static = $this;
		$this->int = 0;
	}

	public function getSelf(): self
	{
		return $this->self;
	}

}
