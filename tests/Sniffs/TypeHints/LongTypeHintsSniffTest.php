<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class LongTypeHintsSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/longTypeHintsNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/longTypeHintsErrors.php');

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 4, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @param annotation.');
		self::assertSniffError($report, 5, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "boolean" in @return annotation.');
		self::assertSniffError($report, 15, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @var annotation.');
		self::assertSniffError($report, 24, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "boolean" in @var annotation.');
		self::assertSniffError($report, 30, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "boolean" in @var annotation.');
		self::assertSniffError($report, 30, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @var annotation.');
		self::assertSniffError($report, 35, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "Boolean" in @param annotation.');
		self::assertSniffError($report, 37, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "Integer" in @param annotation.');
		self::assertSniffError($report, 38, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @return annotation.');

		self::assertAllFixedInFile($report);
	}

}
