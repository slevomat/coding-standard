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

		$this->assertSame(6, $report->getErrorCount());

		$this->assertSniffError($report, 9, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 11, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 23, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 33, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 35, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 47, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

	public function testDeadCatchesInNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/deadCatches.php');

		$this->assertSame(4, $report->getErrorCount());

		$this->assertSniffError($report, 49, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 51, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 61, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 63, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

	public function testDeadUnionCatches()
	{
		$report = $this->checkFile(__DIR__ . '/data/deadUnionCatches.php');

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 31, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		$this->assertSniffError($report, 41, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

}
