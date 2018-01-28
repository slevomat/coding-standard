<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class LongTypeHintsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/longTypeHintsNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/longTypeHintsErrors.php');

		$this->assertSame(10, $report->getErrorCount());

		$this->assertSniffError($report, 4, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @param annotation.');
		$this->assertSniffError($report, 5, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "boolean" in @return annotation.');
		$this->assertSniffError($report, 15, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @var annotation.');
		$this->assertSniffError($report, 24, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "boolean" in @var annotation.');
		$this->assertSniffError($report, 30, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "boolean" in @var annotation.');
		$this->assertSniffError($report, 30, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @var annotation.');
		$this->assertSniffError($report, 35, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "boolean" in @param annotation.');
		$this->assertSniffError($report, 36, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "bool" but found "boolean" in @param annotation.');
		$this->assertSniffError($report, 37, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @param annotation.');
		$this->assertSniffError($report, 38, LongTypeHintsSniff::CODE_USED_LONG_TYPE_HINT, 'Expected "int" but found "integer" in @return annotation.');

		$this->assertAllFixedInFile($report);
	}

}
