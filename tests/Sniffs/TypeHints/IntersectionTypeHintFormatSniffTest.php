<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class IntersectionTypeHintFormatSniffTest extends TestCase
{

	public function testWhitespaceNotSetNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/intersectionTypeHintFormatWhitespaceNotSetNoErrors.php', [
			'enable' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceDisallowedNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/intersectionTypeHintFormatWhitespaceDisallowedNoErrors.php', [
			'enable' => true,
			'withSpaces' => 'no',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceDisallowedErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/intersectionTypeHintFormatWhitespaceDisallowedErrors.php', [
			'enable' => true,
			'withSpaces' => 'no',
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, IntersectionTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE);
		self::assertSniffError(
			$report,
			8,
			IntersectionTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE,
			'Spaces in type hint "\ArrayAccess& \Traversable" are disallowed.'
		);
		self::assertSniffError(
			$report,
			8,
			IntersectionTypeHintFormatSniff::CODE_DISALLOWED_WHITESPACE,
			'Spaces in type hint "\ArrayAccess  & \Traversable &  \Stringable" are disallowed.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testWhitespaceEnabledNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/intersectionTypeHintFormatWhitespaceEnabledNoErrors.php', [
			'enable' => true,
			'withSpaces' => 'yes',
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWhitespaceEnabledErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/intersectionTypeHintFormatWhitespaceEnabledErrors.php', [
			'enable' => true,
			'withSpaces' => 'yes',
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			IntersectionTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE,
			'One space required before and after each "&" in type hint "\ArrayAccess&\Traversable".'
		);
		self::assertSniffError(
			$report,
			8,
			IntersectionTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE,
			'One space required before and after each "&" in type hint "\ArrayAccess& \Traversable".'
		);
		self::assertSniffError(
			$report,
			8,
			IntersectionTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE,
			'One space required before and after each "&" in type hint "\Stringable &\Traversable".'
		);
		self::assertSniffError(
			$report,
			8,
			IntersectionTypeHintFormatSniff::CODE_REQUIRED_WHITESPACE,
			'One space required before and after each "&" in type hint "\ArrayAccess  &    \Traversable &\Stringable".'
		);

		self::assertAllFixedInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/intersectionTypeHintFormatWhitespaceNotSetNoErrors.php', [
			'enable' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

}
