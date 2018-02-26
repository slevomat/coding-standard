<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class EarlyExitSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitNoErrors.php');

		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/earlyExitErrors.php');

		self::assertSame(46, $report->getErrorCount());

		foreach ([6, 15, 24, 33, 42, 50, 58, 66, 74, 82, 90, 98, 108, 149, 157, 165, 191, 199, 207] as $line) {
			self::assertSniffError($report, $line, EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED, 'Use early exit instead of else.');
		}

		foreach ([115, 122, 129, 135, 141, 213, 222, 229, 235, 241, 247, 256, 262, 271, 287, 305] as $line) {
			self::assertSniffError($report, $line, EarlyExitSniff::CODE_EARLY_EXIT_NOT_USED, 'Use early exit to reduce code nesting.');
		}

		foreach ([173, 182, 328, 353] as $line) {
			self::assertSniffError($report, $line, EarlyExitSniff::CODE_USELESS_ELSE, 'Remove useless else to reduce code nesting.');
		}

		foreach ([322, 324, 326, 336, 338, 340, 351] as $line) {
			self::assertSniffError($report, $line, EarlyExitSniff::CODE_USELESS_ELSEIF, 'Remove useless elseif to reduce code nesting.');
		}

		$this->assertAllFixedInFile($report);
	}

	public function testInvalidElseIf(): void
	{
		self::expectException(\Throwable::class);
		self::expectExceptionMessage('Probably invalid "elseif" on line 6.');
		self::checkFile(__DIR__ . '/data/earlyExitInvalidElseIf.php');
	}

}
