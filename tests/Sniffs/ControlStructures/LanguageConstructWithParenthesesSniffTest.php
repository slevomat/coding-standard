<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class LanguageConstructWithParenthesesSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/languageConstructWithParenthesesNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/languageConstructWithParenthesesErrors.php',
			[],
			[LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES]
		);

		self::assertSame(15, $report->getErrorCount());

		self::assertSniffError(
			$report,
			5,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "continue" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			8,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "break" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			12,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "echo" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			13,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "print" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			15,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "include" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			16,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "include_once" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			17,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "require" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			18,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "require_once" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			22,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "return" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			27,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "yield" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			31,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "throw" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			36,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "die" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			37,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "exit" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			41,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "yield from" with parentheses is disallowed.'
		);
		self::assertSniffError(
			$report,
			44,
			LanguageConstructWithParenthesesSniff::CODE_USED_WITH_PARENTHESES,
			'Usage of language construct "include_once" with parentheses is disallowed.'
		);

		self::assertAllFixedInFile($report);
	}

}
