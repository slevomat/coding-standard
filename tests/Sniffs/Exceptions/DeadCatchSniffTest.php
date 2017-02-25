<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

class DeadCatchSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoDeadCatches()
	{
		$report = $this->checkFile(__DIR__ . '/data/noDeadCatches.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testDeadCatchesWithoutNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/deadCatchesWithoutNamespace.php');
		$this->assertNoSniffError($report, 5);
		$this->assertNoSniffError($report, 7);
		$this->assertSniffError($report, 9, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 11, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertNoSniffError($report, 17);
		$this->assertNoSniffError($report, 19);
		$this->assertNoSniffError($report, 21);
		$this->assertSniffError($report, 23, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);

		$this->assertNoSniffError($report, 29);
		$this->assertNoSniffError($report, 31);
		$this->assertSniffError($report, 33, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 35, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertNoSniffError($report, 41);
		$this->assertNoSniffError($report, 43);
		$this->assertNoSniffError($report, 45);
		$this->assertSniffError($report, 47, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

	public function testDeadCatchesInNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/deadCatches.php');
		$this->assertNoSniffError($report, 9);
		$this->assertNoSniffError($report, 15);
		$this->assertNoSniffError($report, 21);
		$this->assertNoSniffError($report, 27);
		$this->assertNoSniffError($report, 33);
		$this->assertNoSniffError($report, 35);
		$this->assertNoSniffError($report, 37);
		$this->assertNoSniffError($report, 39);
		$this->assertNoSniffError($report, 45);
		$this->assertNoSniffError($report, 47);
		$this->assertSniffError($report, 49, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 51, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertNoSniffError($report, 57);
		$this->assertNoSniffError($report, 59);
		$this->assertSniffError($report, 61, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 63, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertNoSniffError($report, 69);
		$this->assertNoSniffError($report, 71);
		$this->assertNoSniffError($report, 73);
		$this->assertNoSniffError($report, 75);
	}

	public function testDeadUnionCatches()
	{
		$report = $this->checkFile(__DIR__ . '/data/deadUnionCatches.php');

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 31, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 41, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

}
