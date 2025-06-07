<?php // lint >= 8.4

class Foo
{

	private $boo = 'boo';
	private $booo = 'booo';

	public string $coo = 'coo';
	public string $cooo = 'cooo';

	public string $doo = 'doo';
	public string $dooo = 'dooo';

	protected $eoo = 'eoo';
	protected $eooo = 0;
	protected $eoooo = 0.0;

	/**
	 * @whatever
	 *
	 * @var int
	 */
	private int $foo = 0;
	/**
	 * @whatever
	 *
	 * @var int
	 */
	private int $fooo = 1;

	private $a = ['a'];
	private $b = ['b'];
	private $c = ['c'];

	private $aa = array('a', 'aa');
	private $bb = array('b', 'bb');

	public private(set) readonly int $readonly1;
	public private(set) readonly int $readonly2;
}
