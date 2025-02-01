<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;
use function range;

class ClassConstantTypeHintSniffTest extends TestCase
{

	public function testNativeTypeHintDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classConstantTypeHintNativeNoErrors.php', [
			'enableNativeTypeHint' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNativeTypeHintNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classConstantTypeHintNativeNoErrors.php', [
			'enableNativeTypeHint' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNativeTypeHintErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classConstantTypeHintNativeErrors.php', [
			'enableNativeTypeHint' => true,
		]);

		self::assertSame(16, $report->getErrorCount());

		foreach (range(6, 16) as $line) {
			self::assertSniffError($report, $line, ClassConstantTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		}
		self::assertSniffError($report, 19, ClassConstantTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		foreach (range(22, 25) as $line) {
			self::assertSniffError($report, $line, ClassConstantTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		}

		self::assertAllFixedInFile($report);
	}

	public function testUselessDocCommentNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classConstantTypeHintUselessDocCommentNoErrors.php', [
			'enableNativeTypeHint' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testUselessDocCommentErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classConstantTypeHintUselessDocCommentErrors.php', [
			'enableNativeTypeHint' => false,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 11, ClassConstantTypeHintSniff::CODE_USELESS_VAR_ANNOTATION);
		self::assertSniffError($report, 23, ClassConstantTypeHintSniff::CODE_USELESS_VAR_ANNOTATION);
		self::assertSniffError($report, 33, ClassConstantTypeHintSniff::CODE_USELESS_DOC_COMMENT);

		self::assertAllFixedInFile($report);
	}

}
