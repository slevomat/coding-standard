<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Whitespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class DuplicateSpacesSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesErrors.php');

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 3, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES);
		self::assertSniffError($report, 7, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 7.');
		self::assertSniffError($report, 7, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 15.');
		self::assertSniffError($report, 7, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 23.');
		self::assertSniffError($report, 9, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 11.');
		self::assertSniffError($report, 9, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 21.');
		self::assertSniffError($report, 9, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 36.');
		self::assertSniffError($report, 9, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 40.');
		self::assertSniffError($report, 11, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES);

		self::assertAllFixedInFile($report);
	}

	public function testIgnoreSpacesBeforeAssignmentNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesIgnoreSpacesBeforeAssignmentNoErrors.php', [
			'ignoreSpacesBeforeAssignment' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testIgnoreSpacesInAnnotationNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesIgnoreSpacesInAnnotationNoErrors.php', [
			'ignoreSpacesInAnnotation' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testIgnoreSpacesInAnnotationErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesIgnoreSpacesInAnnotationErrors.php', [
			'ignoreSpacesInAnnotation' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 21.');

		self::assertAllFixedInFile($report);
	}

	public function testIgnoreSpacesInCommentNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesIgnoreSpacesInCommentNoErrors.php', [
			'ignoreSpacesInComment' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testIgnoreSpacesInCommentErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesIgnoreSpacesInCommentErrors.php', [
			'ignoreSpacesInComment' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 24.');

		self::assertAllFixedInFile($report);
	}

	public function testIgnoreSpacesInParametersNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesIgnoreSpacesInParametersNoErrors.php', [
			'ignoreSpacesInParameters' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testIgnoreSpacesInParametersErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/duplicateSpacesIgnoreSpacesInParametersErrors.php', [
			'ignoreSpacesInParameters' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 7, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 12.');
		self::assertSniffError($report, 7, DuplicateSpacesSniff::CODE_DUPLICATE_SPACES, 'Duplicate spaces at position 15.');

		self::assertAllFixedInFile($report);
	}

}
