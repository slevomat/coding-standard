<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessConstantTypeHintSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessConstantTypeHintNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessConstantTypeHintErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, UselessConstantTypeHintSniff::CODE_USELESS_DOC_COMMENT);
		self::assertSniffError($report, 16, UselessConstantTypeHintSniff::CODE_USELESS_VAR_ANNOTATION);
		self::assertSniffError($report, 28, UselessConstantTypeHintSniff::CODE_USELESS_VAR_ANNOTATION);
		self::assertSniffError($report, 38, UselessConstantTypeHintSniff::CODE_USELESS_DOC_COMMENT);

		self::assertAllFixedInFile($report);
	}

}
