<?php

/**
 * @return Whatever
 */
function simpleFunction() {

}

/**
 * @psalm-type Alias array<
 *   string,
 *   (class-string<Factory\FactoryInterface>|Factory\FactoryInterface)
 *   |callable(ContainerInterface,string,array<mixed>|null)
 * >
 *
 * @template
 */
class WithInvalidTypes
{

	/**
	 * @return Alias
	 */
	public function withInvalidAlias() {

	}

	/**
	 * @return Template
	 */
	public function withInvalidTemplate() {

	}

}

