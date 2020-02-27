<?php // lint >= 7.4

class Foo
{

	private $boo = 'boo', $booo = 'booo';

	public string $coo = 'coo',
		$cooo = 'cooo';

	public string    $doo = 'doo'   , $dooo = 'dooo';

	protected
		$eoo = 'eoo',
		$eooo = 0,
		$eoooo = 0.0
	;

	/**
	 * @whatever
	 *
	 * @var int
	 */
	private int $foo = 0, $fooo = 1;

	private
		$a = ['a'],
		$b = ['b'],
		$c = ['c']
	;

}
