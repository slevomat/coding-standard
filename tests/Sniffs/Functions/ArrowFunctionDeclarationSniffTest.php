<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class ArrowFunctionDeclarationSniffTest extends TestCase
{

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/arrowFunctionDeclarationDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/arrowFunctionDeclarationDefaultSettingsErrors.php');

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_KEYWORD,
			'There must be exactly 1 whitespace after "fn" keyword.'
		);
		self::assertSniffError(
			$report,
			3,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_BEFORE_ARROW,
			'There must be exactly 1 whitespace before =>.'
		);
		self::assertSniffError(
			$report,
			3,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_ARROW,
			'There must be exactly 1 whitespace after =>.'
		);
		self::assertSniffError(
			$report,
			5,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_KEYWORD,
			'There must be exactly 1 whitespace after "fn" keyword.'
		);
		self::assertSniffError(
			$report,
			5,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_BEFORE_ARROW,
			'There must be exactly 1 whitespace before =>.'
		);
		self::assertSniffError(
			$report,
			5,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_ARROW,
			'There must be exactly 1 whitespace after =>.'
		);
		self::assertSniffError(
			$report,
			7,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_KEYWORD,
			'There must be exactly 1 whitespace after "fn" keyword.'
		);
		self::assertSniffError(
			$report,
			9,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_BEFORE_ARROW,
			'There must be exactly 1 whitespace before =>.'
		);
		self::assertSniffError(
			$report,
			9,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_ARROW,
			'There must be exactly 1 whitespace after =>.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/arrowFunctionDeclarationModifiedSettingsNoErrors.php', [
			'spacesCountAfterKeyword' => 0,
			'spacesCountBeforeArrow' => 2,
			'spacesCountAfterArrow' => 3,
			'allowMultiLine' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/arrowFunctionDeclarationModifiedSettingsErrors.php', [
			'spacesCountAfterKeyword' => 0,
			'spacesCountBeforeArrow' => 2,
			'spacesCountAfterArrow' => 3,
			'allowMultiLine' => true,
		]);

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_KEYWORD,
			'There must be no whitespace after "fn" keyword.'
		);
		self::assertSniffError(
			$report,
			3,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_BEFORE_ARROW,
			'There must be exactly 2 whitespaces before =>.'
		);
		self::assertSniffError(
			$report,
			3,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_ARROW,
			'There must be exactly 3 whitespaces after =>.'
		);
		self::assertSniffError(
			$report,
			5,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_KEYWORD,
			'There must be no whitespace after "fn" keyword.'
		);
		self::assertSniffError(
			$report,
			5,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_BEFORE_ARROW,
			'There must be exactly 2 whitespaces before =>.'
		);
		self::assertSniffError(
			$report,
			5,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_ARROW,
			'There must be exactly 3 whitespaces after =>.'
		);
		self::assertSniffError(
			$report,
			7,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_KEYWORD,
			'There must be no whitespace after "fn" keyword.'
		);
		self::assertSniffError(
			$report,
			8,
			ArrowFunctionDeclarationSniff::CODE_INCORRECT_SPACES_AFTER_ARROW,
			'There must be exactly 3 whitespaces after =>.'
		);

		self::assertAllFixedInFile($report);
	}

}
