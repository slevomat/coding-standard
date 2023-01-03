<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireAbstractOrFinalSniffTest extends TestCase
{

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireAbstractOrFinalErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 3, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);
		self::assertSniffError($report, 18, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);
		self::assertSniffError($report, 39, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);
		self::assertSniffError($report, 44, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);
		self::assertSniffError($report, 53, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);

		self::assertAllFixedInFile($report);
	}

}
