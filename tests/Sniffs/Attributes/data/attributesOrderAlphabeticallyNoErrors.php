<?php

#[AttributeA]
#[AttributeB]
#[\Group\AttributeA]
#[\Group\AttributeA('paramA')]
#[\Group\AttributeA('paramS')]
#[\Group\AttributeB]
class Whatever
{

	#[AttributeA]
	#[AttributeB]
	#[UnknownOrder]
	public function method()
	{
	}

	#[AttributeA]
	#[AttributeB]
	#[\Group\AttributeA]
	#[\Group\AttributeA('paramA')]
	#[\Group\AttributeA('paramS')]
	#[\Group\AttributeB]
	public function method2()
	{
	}
}
