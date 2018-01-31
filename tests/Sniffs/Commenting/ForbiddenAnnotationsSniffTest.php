<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

class ForbiddenAnnotationsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoForbiddenAnnotations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noForbiddenAnnotations.php', [
			'forbiddenAnnotations' => ['@see', '@throws'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testForbiddenAnnotations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenAnnotations.php', [
			'forbiddenAnnotations' => ['@see', '@throws', '@Route'],
		]);

		self::assertSame(7, $report->getErrorCount());

		self::assertSniffError($report, 5, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		self::assertSniffError($report, 6, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		self::assertSniffError($report, 19, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		self::assertSniffError($report, 21, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		self::assertSniffError($report, 30, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		self::assertSniffError($report, 32, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		self::assertSniffError($report, 45, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
	}

	public function testFixableForbiddenAnnotations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenAnnotations.php', [
			'forbiddenAnnotations' => ['@see', '@throws', '@Route'],
		], [ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN]);
		self::assertAllFixedInFile($report);
	}

}
