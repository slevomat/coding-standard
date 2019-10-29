<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class NamespaceSpacingSniffTest extends TestCase
{

	public function testNoNamespaceNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingNoNamespace.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testNamespaceWithCurlyBraces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingNamespaceWithCurlyBraces.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingWithDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingWithDefaultSettingsErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 2, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE);
		self::assertSniffError($report, 2, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_NAMESPACE);

		self::assertAllFixedInFile($report);
	}

	public function testAfterOpenTagNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingAfterOpenTagNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testAfterOpenTagErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingAfterOpenTagErrors.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 2, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE);

		self::assertAllFixedInFile($report);
	}

	public function testAfterLineCommentNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingAfterLineCommentNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testAfterLineCommentErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingAfterLineCommentErrors.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingWithModifiedSettingsNoErrors.php', [
			'linesCountBeforeNamespace' => 0,
			'linesCountAfterNamespace' => 2,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingWithModifiedSettingsErrors.php', [
			'linesCountBeforeNamespace' => 0,
			'linesCountAfterNamespace' => 2,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 4, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE);
		self::assertSniffError($report, 4, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_NAMESPACE);

		self::assertAllFixedInFile($report);
	}

	public function testNoLineBeforeNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingNoLineBeforeNamespace.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE);
	}

	public function testNoLineAfterNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingNoLineAfterNamespace.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_NAMESPACE);
	}

	public function testNoCodeAfterNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingNoCodeAfterNamespace.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testPhpcsCommentBefore(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingWithPhpcsCommentBefore.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE);

		self::assertAllFixedInFile($report);
	}

	public function testFileCommentBefore(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingFileCommentBefore.php', [
			'linesCountBeforeNamespace' => 2,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 6, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE);

		self::assertAllFixedInFile($report);
	}

	public function testInvalidFileCommentBeforeNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingInvalidFileCommentBeforeNoErrors.php', [
			'linesCountBeforeNamespace' => 1,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testInvalidFileCommentBefore(): void
	{
		$report = self::checkFile(__DIR__ . '/data/namespaceSpacingInvalidFileCommentBefore.php', [
			'linesCountBeforeNamespace' => 2,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 6, NamespaceSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE);

		self::assertAllFixedInFile($report);
	}

}
