<?php

#[AttributeA]
#[AttributeB]
#[\Group\AttributeA('paramS')]
#[\Group\AttributeA]
#[\Group\AttributeA('paramA')]
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
	#[\Group\AttributeA('paramS')]
	#[\Group\AttributeA]
	#[\Group\AttributeA('paramA')]
	#[\Group\AttributeB]
	public function method2()
	{
	}
}
