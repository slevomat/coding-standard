<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class LongTypeHintsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/longTypeHintsNoErrors.php'));
	}

	public function testErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/longTypeHintsErrors.php');

		$this->assertSame(6, $report->getErrorCount());

		$this->assertSniffError($report, 7, LongTypeHintsSniff::CODE_USED_LONG_TYPE, 'Expected "int" but found "integer" in @param annotation.');
		$this->assertSniffError($report, 7, LongTypeHintsSniff::CODE_USED_LONG_TYPE, 'Expected "bool" but found "boolean" in @return annotation.');
		$this->assertSniffError($report, 16, LongTypeHintsSniff::CODE_USED_LONG_TYPE, 'Expected "int" but found "integer" in @var annotation.');
		$this->assertSniffError($report, 19, LongTypeHintsSniff::CODE_USED_LONG_TYPE, 'Expected "bool" but found "boolean" in @var annotation.');
		$this->assertSniffError($report, 25, LongTypeHintsSniff::CODE_USED_LONG_TYPE, 'Expected "bool" but found "boolean" in @param annotation.');
		$this->assertSniffError($report, 25, LongTypeHintsSniff::CODE_USED_LONG_TYPE, 'Expected "int" but found "integer" in @return annotation.');
	}

}
