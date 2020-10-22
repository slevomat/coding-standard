<?php

class TooManyPropertiesClass
{
	/**
	 * @var array
	 */
	protected $array1;

	/**
	 * @var bool
	 */
	protected $boolean1;

	/**
	 * @var boolean
	 */
	protected $boolean2;

	/**
	 * @var callable
	 */
	protected $callable1;

	/**
	 * @var double
	 */
	protected $double1;

	/**
	 * @var float
	 */
	protected $float1;

	/**
	 * @var int
	 */
	protected $integer1;

	/**
	 * @var integer
	 */
	protected $integer2;

	/**
	 * @var resource
	 */
	protected $resource1;

	/**
	 * @var string
	 */
	protected $string1;

	/**
	 * @var string
	 */
	protected $string2;
}

function tooManyPropertiesAnonymousClass()
{
	return new class {
		/**
		 * @var array
		 */
		protected $array1;

		/**
		 * @var bool
		 */
		protected $boolean1;

		/**
		 * @var boolean
		 */
		protected $boolean2;

		/**
		 * @var callable
		 */
		protected $callable1;

		/**
		 * @var double
		 */
		protected $double1;

		/**
		 * @var float
		 */
		protected $float1;

		/**
		 * @var int
		 */
		protected $integer1;

		/**
		 * @var integer
		 */
		protected $integer2;

		/**
		 * @var resource
		 */
		protected $resource1;

		/**
		 * @var string
		 */
		protected $string1;

		/**
		 * @var string
		 */
		protected $string2;
	};
}
