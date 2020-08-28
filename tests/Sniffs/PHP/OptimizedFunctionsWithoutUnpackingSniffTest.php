<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class OptimizedFunctionsWithoutUnpackingSniffTest extends TestCase
{

	public function testNoErrorsNamespaced(): void
	{
		$report = self::checkFile(__DIR__ . '/data/optimizedFunctionsWithoutUnpackingNamespacedNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsNotNamespaced(): void
	{
		$report = self::checkFile(__DIR__ . '/data/optimizedFunctionsWithoutUnpackingNamespacedNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsNamespaced(): void
	{
		$report = self::checkFile(__DIR__ . '/data/optimizedFunctionsWithoutUnpackingNamespacedErrors.php');

		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError($report, 10, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 11, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 12, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 14, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 15, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 16, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 17, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 24, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 25, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 32, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 33, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 34, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
	}

	public function testErrorsNotNamespaced(): void
	{
		$report = self::checkFile(__DIR__ . '/data/optimizedFunctionsWithoutUnpackingNotNamespacedErrors.php');

		self::assertSame(10, $report->getErrorCount());

		self::assertSniffError($report, 3, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 4, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 5, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 7, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 9, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 10, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 17, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 18, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
		self::assertSniffError($report, 25, OptimizedFunctionsWithoutUnpackingSniff::CODE_UNPACKING_USED);
	}

}
