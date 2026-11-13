<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\MultiLineFunctionDeclarationSniff;
use SlevomatCodingStandard\Sniffs\TestCase;
use const T_BOOLEAN_AND;
use const T_STRING_CONCAT;

/**
 * Tests for SlevomatCodingStandard\Sniffs\Operators\MultiLineOperatorIndentationSniff.
 */
class MultiLineOperatorIndentationSniffTest extends TestCase
{

	/**
	 * Verifies that valid single-line and multiline operator expressions produce no sniff errors.
	 */
	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multiLineOperatorIndentationNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	/**
	 * Verifies that explicitly configured operator token names and token codes are resolved and checked.
	 */
	public function testCustomOperators(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/multiLineOperatorIndentationNoErrors.php',
			['operators' => ['T_STRING_CONCAT', T_STRING_CONCAT, T_BOOLEAN_AND]],
		);
		self::assertNoSniffErrorInFile($report);
	}

	/**
	 * Verifies that trailing multiline operators and improperly indented leading operators are reported
	 * and automatically fixed.
	 */
	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/multiLineOperatorIndentationErrors.php');

		self::assertSame(18, $report->getErrorCount());

		self::assertSniffError($report, 5, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 6, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 7, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 9, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 10, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 15, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 16, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 23, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 28, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 29, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 30, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 38, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 39, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 46, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 55, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 59, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 62, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 68, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);

		self::assertAllFixedInFile($report);
	}

	/**
	 * Verifies that multiline operator continuation lines in class constants and multiline function
	 * parameter declarations do not conflict with Generic.WhiteSpace.ScopeIndent (exact = true) or
	 * Squiz.Functions.MultiLineFunctionDeclaration.
	 */
	public function testNoConflictWithScopeIndentAndMultiLineFunctionDeclaration(): void
	{
		$codeSniffer = $this->createScopeIndentRunner();

		$file = new LocalFile(__DIR__ . '/data/multiLineOperatorIndentationNoErrors.php', $codeSniffer->ruleset, $codeSniffer->config);
		$file->process();

		self::assertNoSniffErrorInFile($file);
	}

	/**
	 * Verifies that Generic.WhiteSpace.ScopeIndent continues reporting unindented match arm
	 * expression-start lines when MultiLineOperatorIndentationSniff does not validate them.
	 */
	public function testScopeIndentNotSuppressedForUnvalidatedMatchArmExpressions(): void
	{
		$codeSniffer = $this->createScopeIndentRunner();

		$code = '<?php
function ($x, $value) {
	return match ($x) {
		default =>
$value . \'suffix\',
		1 =>
$value
	. \'suffix\',
	};
};';

		$file = new DummyFile($code, $codeSniffer->ruleset, $codeSniffer->config);
		$file->process();

		$errors = $file->getErrors();
		self::assertArrayHasKey(5, $errors);
		self::assertArrayHasKey(7, $errors);
		self::assertSame('Generic.WhiteSpace.ScopeIndent.IncorrectExact', $errors[5][1][0]['source']);
		self::assertSame('Generic.WhiteSpace.ScopeIndent.IncorrectExact', $errors[7][1][0]['source']);

		$validCode = '<?php
function ($x, $value) {
	return match ($x) {
		default =>
			$value
				. \'suffix\',
	};
};';

		$validFile = new DummyFile($validCode, $codeSniffer->ruleset, $codeSniffer->config);
		$validFile->process();

		self::assertNoSniffErrorInFile($validFile);
	}

	/**
	 * Creates a CodeSniffer runner configured with ScopeIndent, MultiLineFunctionDeclaration,
	 * and MultiLineOperatorIndentation sniffs.
	 */
	private function createScopeIndentRunner(): Runner
	{
		$codeSniffer = new Runner();
		$codeSniffer->config = new Config(['-s']);
		$codeSniffer->init();

		$codeSniffer->ruleset->ruleset['Generic.WhiteSpace.ScopeIndent']['properties'] = [
			'exact' => [
				'value' => true,
				'scope' => 'sniff',
			],
			'tabIndent' => [
				'value' => true,
				'scope' => 'sniff',
			],
		];

		$codeSniffer->ruleset->sniffs = [
			ScopeIndentSniff::class => new ScopeIndentSniff(),
			MultiLineFunctionDeclarationSniff::class => new MultiLineFunctionDeclarationSniff(),
			MultiLineOperatorIndentationSniff::class => new MultiLineOperatorIndentationSniff(),
		];

		$codeSniffer->ruleset->populateTokenListeners();
		$codeSniffer->config->tabWidth = 4;

		return $codeSniffer;
	}

}
