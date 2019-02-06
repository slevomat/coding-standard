<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowArrayTypeHintSyntaxSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/disallowArrayTypeHintSyntaxNoErrors.php'));
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowArrayTypeHintSyntaxErrors.php', [
			'traversableTypeHints' => [
				'\ArrayObject',
				'\Traversable',
			],
		]);

		self::assertSame(21, $report->getErrorCount());

		self::assertSniffError($report, 6, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "\DateTimeImmutable[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 7, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "bool[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 7, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 12, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "(\DateTimeImmutable[]|\DateTime[]|null)[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 12, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "\DateTimeImmutable[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 12, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "\DateTime[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 15, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 18, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 21, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "bool[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 21, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 24, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[][][]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 27, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 30, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[][]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 33, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "bool[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 36, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 39, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "string[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 42, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "int[]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 45, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "string[][]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 49, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "string[][]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 53, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "string[][]" is disallowed, use generic type hint syntax instead.');
		self::assertSniffError($report, 57, DisallowArrayTypeHintSyntaxSniff::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX, 'Usage of array type hint syntax in "string[][]" is disallowed, use generic type hint syntax instead.');

		self::assertAllFixedInFile($report);
	}

}
