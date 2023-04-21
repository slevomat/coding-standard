<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class AnnotationNameSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/annotationNameNoErrors.php');

		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/annotationNameErrors.php');

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError(
			$report,
			4,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @deprecated, found @Deprecated.'
		);
		self::assertSniffError(
			$report,
			10,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @param, found @pArAm.'
		);
		self::assertSniffError(
			$report,
			11,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @phpstan-return, found @PHPSTAN-return.'
		);
		self::assertSniffError(
			$report,
			18,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @inheritDoc, found @inheritdoc.'
		);
		self::assertSniffError(
			$report,
			17,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @inheritDoc, found @inheritdoc.'
		);
		self::assertSniffError(
			$report,
			17,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @inheritDoc, found @INHERITDOC.'
		);
		self::assertSniffError(
			$report,
			26,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @inheritDoc, found @inheritdoc.'
		);
		self::assertSniffError(
			$report,
			31,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @inheritDoc, found @inheritdoc.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testWithModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/annotationNameWithModifiedSettingsNoErrors.php', [
			'annotations' => [
				'Deprecated',
				'PARAM',
				'PHPSTAN-RETURN',
				'inheritdoc',
			],
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testWithModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/annotationNameWithModifiedSettingsErrors.php', [
			'annotations' => [
				'@Deprecated',
				'@PARAM',
				'@PHPSTAN-RETURN',
				'@inheritdoc',
			],
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError(
			$report,
			4,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @Deprecated, found @deprecated.'
		);
		self::assertSniffError(
			$report,
			10,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @PARAM, found @param.'
		);
		self::assertSniffError(
			$report,
			11,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @PHPSTAN-RETURN, found @phpstan-return.'
		);
		self::assertSniffError(
			$report,
			18,
			AnnotationNameSniff::CODE_ANNOTATION_NAME_INCORRECT,
			'Annotation name is incorrect. Expected @inheritdoc, found @inheritDoc.'
		);

		self::assertAllFixedInFile($report);
	}

}
