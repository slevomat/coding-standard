<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class NullabilitySymbolSpacingSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/nullabilitySymbolSpacingNoErrors.php'));
	}

	public function testErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/nullabilitySymbolSpacingErrors.php');

		$this->assertSame(8, $report->getErrorCount());

		$this->assertSniffError($report, 3, NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT, '"\DateTimeImmutable"');
		$this->assertSniffError($report, 3, NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT, '"\Foo\Boo\Doo"');
		$this->assertSniffError($report, 8, NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT, '"string"');
		$this->assertSniffError($report, 8, NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT, '"self"');
		$this->assertSniffError($report, 16, NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT, '"int"');
		$this->assertSniffError($report, 16, NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT, '"array"');
		$this->assertSniffError($report, 23, NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT, '"bool"');
		$this->assertSniffError($report, 23, NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT, '"callable"');
	}

	public function testFixableWhitespaceBetweenNullabilitySymbolAndTypeHint()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableWhitespaceBetweenNullabilitySymbolAndTypeHint.php', [], [NullabilitySymbolSpacingSniff::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT]);
		$this->assertAllFixedInFile($report);
	}

}
