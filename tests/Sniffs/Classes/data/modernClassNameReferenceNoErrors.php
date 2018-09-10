<?php

class Whatever
{
	public function getClassWithDifferentParameterThanThis()
	{
		$self = $this;
		return get_class($self);
	}

	public function getParentClassWithDifferentParameterThanThis()
	{
		$self = $this;
		return get_parent_class($self);
	}

	public function anotherMethodName()
	{
		return max(0, 1);
	}

	public function noMethod()
	{
		return $this->get_class * $this->get_parent_class * $this->get_called_class;
	}

	public function classMethod()
	{
		return $this->get_class() * $this->get_parent_class() * $this->get_called_class();
	}

	public function getClassWithMoreThanThisParameter()
	{
		return get_class($this->anything);
	}

	public function getParentClassWithMoreThanThisParameter()
	{
		return get_parent_class($this->anything);
	}

	public function getClassWithSomethingElse()
	{
		return get_class(new stdClass());
	}

	public function getParentClassWithSomethingElse()
	{
		return get_parent_class(new stdClass());
	}

	public function getParentClassWithThis()
	{
		return get_parent_class($this);
	}

}
