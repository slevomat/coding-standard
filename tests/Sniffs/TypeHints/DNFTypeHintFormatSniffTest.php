<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class DNFTypeHintFormatSniffTest extends TestCase
{

	public function testWhitespaceAroundOperatorsNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceAroundOperatorsNotSetNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceAroundOperatorsDisallowedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceAroundOperatorsDisallowedNoErrors.php', [
			'enable' => true,
			'withSpacesAroundOperators' => 'no',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceAroundOperatorsDisallowedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceAroundOperatorsDisallowedErrors.php', [
			'enable' => true,
			'withSpacesAroundOperators' => 'no',
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			'Spaces around "|" or "&" in type hint "Bar| ( Foo & Boo   )" are disallowed.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			'Spaces around "|" or "&" in type hint "( Foo &Boo)|Bar" are disallowed.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			'Spaces around "|" or "&" in type hint "(   Foo&Boo ) | (  Bar&Baz   )" are disallowed.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testWhitespaceAroundOperatorsRequiredNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceAroundOperatorsRequiredNoErrors.php', [
			'enable' => true,
			'withSpacesAroundOperators' => 'yes',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceAroundOperatorsRequiredErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceAroundOperatorsRequiredErrors.php', [
			'enable' => true,
			'withSpacesAroundOperators' => 'yes',
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			'One space required around each "|" or "&" in type hint "( Foo&Boo   )| Bar".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			'One space required around each "|" or "&" in type hint "( Foo&Boo)|Bar".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			'One space required around each "|" or "&" in type hint "(   Foo &Boo )| (  Bar& Baz   )".',
		);

		self::assertAllFixedInFile($report);
	}

	public function testWhitespaceInsideParenthesesNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceInsideParenthesesNotSetNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceInsideParenthesesDisallowedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceInsideParenthesesDisallowedNoErrors.php', [
			'enable' => true,
			'withSpacesInsideParentheses' => 'no',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceInsideParenthesesDisallowedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceInsideParenthesesDisallowedErrors.php', [
			'enable' => true,
			'withSpacesInsideParentheses' => 'no',
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES,
			'Spaces inside parentheses in type hint "( Foo&Boo   ) |   Bar" are disallowed.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES,
			'Spaces inside parentheses in type hint "(Foo&Boo )|Bar" are disallowed.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES,
			'Spaces inside parentheses in type hint "(   Foo&Boo )|(  Bar & Baz   )" are disallowed.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testWhitespaceInsideParenthesesRequiredNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceInsideParenthesesRequiredNoErrors.php', [
			'enable' => true,
			'withSpacesInsideParentheses' => 'yes',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceInsideParenthesesRequiredErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatWhitespaceInsideParenthesesRequiredErrors.php', [
			'enable' => true,
			'withSpacesInsideParentheses' => 'yes',
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES,
			'One space required around expression inside parentheses in type hint "( Foo&Boo  ) |   Bar".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES,
			'One space required around expression inside parentheses in type hint "(    Foo&Boo)|Bar".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES,
			'One space required around expression inside parentheses in type hint "(    Foo&Boo   )|(Bar & Baz)".',
		);

		self::assertAllFixedInFile($report);
	}

	public function testShortNullableNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatShortNullableNotSetNoErrors.php', [
			'enable' => true,
		], [DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE, DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testShortNullableRequiredNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatShortNullableRequiredNoErrors.php', [
			'enable' => true,
			'shortNullable' => 'yes',
		], [DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testShortNullableRequiredErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatShortNullableRequiredErrors.php', [
			'enable' => true,
			'shortNullable' => 'yes',
		], [DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE,
			'Short nullable type hint in "null|bool" is required.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE,
			'Short nullable type hint in "string|null" is required.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testShortNullableDisallowedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatShortNullableDisallowedNoErrors.php', [
			'enable' => true,
			'shortNullable' => 'no',
		], [DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testShortNullableDisallowedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatShortNullableDisallowedErrors.php', [
			'enable' => true,
			'shortNullable' => 'no',
		], [DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE,
			'Usage of short nullable type hint in "?bool" is disallowed.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE,
			'Usage of short nullable type hint in "?string" is disallowed.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testNullPositionNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatNullPositionNotSetNoErrors.php', [
			'enable' => true,
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION, DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNullPositionFirstNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatNullPositionFirstNoErrors.php', [
			'enable' => true,
			'nullPosition' => 'first',
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNullPositionFirstErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatNullPositionFirstErrors.php', [
			'enable' => true,
			'nullPosition' => 'first',
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION,
			'Null type hint should be on first position in "bool|null|int".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION,
			'Null type hint should be on first position in "string|null|\Anything".',
		);

		self::assertAllFixedInFile($report);
	}

	public function testNullPositionLastNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatNullPositionLastNoErrors.php', [
			'enable' => true,
			'nullPosition' => 'last',
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNullPositionLastErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatNullPositionLastErrors.php', [
			'enable' => true,
			'nullPosition' => 'last',
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "bool|null|int".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "string|null|\Anything".',
		);

		self::assertAllFixedInFile($report);
	}

	public function testDocBlockWhitespaceAroundOperatorsNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceAroundOperatorsNotSetNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockWhitespaceAroundOperatorsDisallowedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceAroundOperatorsDisallowedNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'withSpacesAroundOperators' => 'no',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockWhitespaceAroundOperatorsDisallowedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceAroundOperatorsDisallowedErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'withSpacesAroundOperators' => 'no',
		]);

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			'Spaces around "|" or "&" in type hint "null | string" are disallowed.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			'Spaces around "|" or "&" in type hint "int | null" are disallowed.',
		);
		self::assertSniffError(
			$report,
			15,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			'Spaces around "|" or "&" in type hint "bool | null" are disallowed.',
		);
		self::assertSniffError(
			$report,
			25,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			'Spaces around "|" or "&" in type hint "(Foo & Bar) | null" are disallowed.',
		);
		self::assertSniffError(
			$report,
			25,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			'Spaces around "|" or "&" in type hint "(Foo & Bar)" are disallowed.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testDocBlockWhitespaceAroundOperatorsRequiredNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceAroundOperatorsRequiredNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'withSpacesAroundOperators' => 'yes',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockWhitespaceAroundOperatorsRequiredErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceAroundOperatorsRequiredErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'withSpacesAroundOperators' => 'yes',
		]);

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			'One space required around each "|" or "&" in type hint "null|string".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			'One space required around each "|" or "&" in type hint "int|null".',
		);
		self::assertSniffError(
			$report,
			15,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			'One space required around each "|" or "&" in type hint "bool|null".',
		);
		self::assertSniffError(
			$report,
			25,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			'One space required around each "|" or "&" in type hint "(Foo&Bar)|null".',
		);
		self::assertSniffError(
			$report,
			25,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			'One space required around each "|" or "&" in type hint "(Foo&Bar)".',
		);

		self::assertAllFixedInFile($report);
	}

	public function testDocBlockWhitespaceInsideParenthesesNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceInsideParenthesesNotSetNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockWhitespaceInsideParenthesesDisallowedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceInsideParenthesesDisallowedNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'withSpacesInsideParentheses' => 'no',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockWhitespaceInsideParenthesesDisallowedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceInsideParenthesesDisallowedErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'withSpacesInsideParentheses' => 'no',
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES,
			'Spaces inside parentheses in type hint "( Foo&Bar )" are disallowed.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES,
			'Spaces inside parentheses in type hint "( Baz&Qux )" are disallowed.',
		);
		self::assertSniffError(
			$report,
			15,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES,
			'Spaces inside parentheses in type hint "( Foo&Bar )" are disallowed.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testDocBlockWhitespaceInsideParenthesesRequiredNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceInsideParenthesesRequiredNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'withSpacesInsideParentheses' => 'yes',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockWhitespaceInsideParenthesesRequiredErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockWhitespaceInsideParenthesesRequiredErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'withSpacesInsideParentheses' => 'yes',
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES,
			'One space required around expression inside parentheses in type hint "(Foo&Bar)".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES,
			'One space required around expression inside parentheses in type hint "(Baz&Qux)".',
		);
		self::assertSniffError(
			$report,
			15,
			DNFTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES,
			'One space required around expression inside parentheses in type hint "(Foo&Bar)".',
		);

		self::assertAllFixedInFile($report);
	}

	public function testDocBlockShortNullableNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockShortNullableNotSetNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
		], [DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE, DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockShortNullableRequiredNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockShortNullableRequiredNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'shortNullable' => 'yes',
		], [DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockShortNullableRequiredErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockShortNullableRequiredErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'shortNullable' => 'yes',
		], [DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE,
			'Short nullable type hint in "string|null" is required.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE,
			'Short nullable type hint in "int|null" is required.',
		);
		self::assertSniffError(
			$report,
			15,
			DNFTypeHintFormatSniff::CODE_REQUIRED_SHORT_NULLABLE,
			'Short nullable type hint in "bool|null" is required.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testDocBlockShortNullableDisallowedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockShortNullableDisallowedNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'shortNullable' => 'no',
		], [DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockShortNullableDisallowedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockShortNullableDisallowedErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'shortNullable' => 'no',
		], [DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE,
			'Usage of short nullable type hint in "?string" is disallowed.',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE,
			'Usage of short nullable type hint in "?int" is disallowed.',
		);
		self::assertSniffError(
			$report,
			15,
			DNFTypeHintFormatSniff::CODE_DISALLOWED_SHORT_NULLABLE,
			'Usage of short nullable type hint in "?bool" is disallowed.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testDocBlockNullPositionNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockNullPositionNotSetNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION, DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockNullPositionFirstNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockNullPositionFirstNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'nullPosition' => 'first',
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockNullPositionFirstErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockNullPositionFirstErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'nullPosition' => 'first',
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION,
			'Null type hint should be on first position in "string|null".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION,
			'Null type hint should be on first position in "int|null".',
		);
		self::assertSniffError(
			$report,
			15,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION,
			'Null type hint should be on first position in "bool|null".',
		);

		self::assertAllFixedInFile($report);
	}

	public function testDocBlockNullPositionLastNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockNullPositionLastNoErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'nullPosition' => 'last',
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDocBlockNullPositionLastErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockNullPositionLastErrors.php', [
			'enable' => true,
			'enableForDocComments' => true,
			'nullPosition' => 'last',
		], [DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|string".',
		);
		self::assertSniffError(
			$report,
			8,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|int".',
		);
		self::assertSniffError(
			$report,
			15,
			DNFTypeHintFormatSniff::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			'Null type hint should be on last position in "null|bool".',
		);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportDocCommentsIfNotEnabledForDocComments(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatDocBlockShortNullableRequiredErrors.php', [
			'enable' => true,
			'withSpacesAroundOperators' => 'yes',
			'withSpacesInsideParentheses' => 'yes',
			'shortNullable' => 'yes',
			'nullPosition' => 'last',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/dnfTypeHintFormatShortNullableNotSetNoErrors.php', [
			'enable' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

}
