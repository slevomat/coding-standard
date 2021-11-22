<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireAbstractOrFinalSniffTest extends TestCase
{

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireAbstractOrFinalErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 2, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);
		self::assertSniffError($report, 17, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);
		self::assertSniffError($report, 38, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);
		self::assertSniffError($report, 44, RequireAbstractOrFinalSniff::CODE_NO_ABSTRACT_OR_FINAL);

		self::assertAllFixedInFile($report);
	}

}
