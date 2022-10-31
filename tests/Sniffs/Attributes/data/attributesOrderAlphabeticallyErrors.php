<?php

#[AttributeB]
#[\Group\AttributeB]
#[\Group\AttributeA('paramS')]
#[AttributeA]
#[\Group\AttributeA]
#[\Group\AttributeA('paramA')]
class Whatever
{

	#[UnknownOrder] #[AttributeB]
	#[AttributeA]
	public function method()
	{
	}

	#[AttributeB]
	#[\Group\AttributeB]
	#[\Group\AttributeA('paramS')]
	#[AttributeA]
	#[\Group\AttributeA]
	#[\Group\AttributeA('paramA')]
	public function method2()
	{
	}
}
