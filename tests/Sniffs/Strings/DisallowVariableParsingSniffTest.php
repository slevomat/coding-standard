<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Strings;

use SlevomatCodingStandard\Sniffs\TestCase;
use UnexpectedValueException;

class DisallowVariableParsingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowVariableParsingNoError.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsDollarCurlySyntax(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/disallowVariableParsingErrors.php',
			[
				'disallowDollarCurlySyntax' => true,
				'disallowCurlyDollarSyntax' => false,
				'disallowSimpleSyntax' => false,
			],
		);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError(
			$report,
			10,
			DisallowVariableParsingSniff::CODE_DISALLOWED_DOLLAR_CURLY_SYNTAX,
			'Using variable syntax "${...}" inside string is disallowed as syntax "${...}" is deprecated as of PHP 8.2, found "${simpleString}".',
		);

		self::assertSniffError(
			$report,
			11,
			DisallowVariableParsingSniff::CODE_DISALLOWED_DOLLAR_CURLY_SYNTAX,
			'Using variable syntax "${...}" inside string is disallowed as syntax "${...}" is deprecated as of PHP 8.2, found "${array[1]}".',
		);

		self::assertSniffError(
			$report,
			17,
			DisallowVariableParsingSniff::CODE_DISALLOWED_DOLLAR_CURLY_SYNTAX,
			'Using variable syntax "${...}" inside string is disallowed as syntax "${...}" is deprecated as of PHP 8.2, found "${simpleString}".',
		);

		self::assertSniffError(
			$report,
			18,
			DisallowVariableParsingSniff::CODE_DISALLOWED_DOLLAR_CURLY_SYNTAX,
			'Using variable syntax "${...}" inside string is disallowed as syntax "${...}" is deprecated as of PHP 8.2, found "${array[1]}".',
		);
	}

	public function testErrorsCurlyDollarSyntax(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/disallowVariableParsingErrors.php',
			[
				'disallowCurlyDollarSyntax' => true,
				'disallowDollarCurlySyntax' => false,
				'disallowSimpleSyntax' => false,
			],
		);

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError(
			$report,
			22,
			DisallowVariableParsingSniff::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
			'Using variable syntax "{$...}" inside string is disallowed, found "{$simpleString}".',
		);

		self::assertSniffError(
			$report,
			23,
			DisallowVariableParsingSniff::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
			'Using variable syntax "{$...}" inside string is disallowed, found "{$array[1]}".',
		);

		self::assertSniffError(
			$report,
			24,
			DisallowVariableParsingSniff::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
			'Using variable syntax "{$...}" inside string is disallowed, found "{$object->name}".',
		);

		self::assertSniffError(
			$report,
			31,
			DisallowVariableParsingSniff::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
			'Using variable syntax "{$...}" inside string is disallowed, found "{$simpleString}".',
		);

		self::assertSniffError(
			$report,
			32,
			DisallowVariableParsingSniff::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
			'Using variable syntax "{$...}" inside string is disallowed, found "{$array[1]}".',
		);

		self::assertSniffError(
			$report,
			33,
			DisallowVariableParsingSniff::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
			'Using variable syntax "{$...}" inside string is disallowed, found "{$object->name}".',
		);

		self::assertSniffError(
			$report,
			51,
			DisallowVariableParsingSniff::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
			'Using variable syntax "{$...}" inside string is disallowed, found "{$array[$simpleString]}".',
		);

		self::assertSniffError(
			$report,
			53,
			DisallowVariableParsingSniff::CODE_DISALLOWED_CURLY_DOLLAR_SYNTAX,
			'Using variable syntax "{$...}" inside string is disallowed, found "{$a->test($b)}".',
		);
	}

	public function testErrorsSimpleSyntax(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/disallowVariableParsingErrors.php',
			[
				'disallowSimpleSyntax' => true,
				'disallowDollarCurlySyntax' => false,
				'disallowCurlyDollarSyntax' => false,
			],
		);

		self::assertSame(7, $report->getErrorCount());

		self::assertSniffError(
			$report,
			37,
			DisallowVariableParsingSniff::CODE_DISALLOWED_SIMPLE_SYNTAX,
			'Using variable syntax "$..." inside string is disallowed, found "$simpleString".',
		);

		self::assertSniffError(
			$report,
			38,
			DisallowVariableParsingSniff::CODE_DISALLOWED_SIMPLE_SYNTAX,
			'Using variable syntax "$..." inside string is disallowed, found "$array".',
		);

		self::assertSniffError(
			$report,
			39,
			DisallowVariableParsingSniff::CODE_DISALLOWED_SIMPLE_SYNTAX,
			'Using variable syntax "$..." inside string is disallowed, found "$object".',
		);

		self::assertSniffError(
			$report,
			46,
			DisallowVariableParsingSniff::CODE_DISALLOWED_SIMPLE_SYNTAX,
			'Using variable syntax "$..." inside string is disallowed, found "$simpleString".',
		);

		self::assertSniffError(
			$report,
			47,
			DisallowVariableParsingSniff::CODE_DISALLOWED_SIMPLE_SYNTAX,
			'Using variable syntax "$..." inside string is disallowed, found "$array".',
		);

		self::assertSniffError(
			$report,
			48,
			DisallowVariableParsingSniff::CODE_DISALLOWED_SIMPLE_SYNTAX,
			'Using variable syntax "$..." inside string is disallowed, found "$object".',
		);

		self::assertSniffError(
			$report,
			51,
			DisallowVariableParsingSniff::CODE_DISALLOWED_SIMPLE_SYNTAX,
			'Using variable syntax "$..." inside string is disallowed, found "$simpleString".',
		);
	}

	public function testNoOptionIsSet(): void
	{
		$this->expectException(UnexpectedValueException::class);
		$this->expectExceptionMessage('No option is set.');

		self::checkFile(__DIR__ . '/data/disallowVariableParsingNoError.php', [
			'disallowDollarCurlySyntax' => false,
			'disallowCurlyDollarSyntax' => false,
			'disallowSimpleSyntax' => false,
		]);
	}

}
