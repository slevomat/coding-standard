<?php // lint >= 8.1

class Whatever
{

	public const ONE = 1;


	private const TWO = 2;

	/**
	 * @var int
	 */
	public $one = 1;
	/** @var string|null */
	private $two;

	/**
	 * @return object
	 */
	public function one()
	{
	}



	public function two()
	{

	} // Fucking comment

	use SomeTrait;
	use AnotherTrait;

	/** @return void */
	public function third()
	{

	} /* Fucking comment */

	static $third;

	final const THIRD = 'third';

	readonly int $forth;

}

enum Gender: string
{

	use LabeledEnumTrait;

	case FEMALE = 'female';
	case MALE = 'male';
	case UNSPECIFIED = 'unspecified';

	/**
	 * @return string[]
	 */
	public static function getLabelDefinitions(): array
	{
		return [
			self::FEMALE->value => 'man',
			self::MALE->value => 'woman',
			self::UNSPECIFIED->value => 'unspecified',
		];
	}

}
