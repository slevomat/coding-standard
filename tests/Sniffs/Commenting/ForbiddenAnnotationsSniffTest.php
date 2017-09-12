<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

class ForbiddenAnnotationsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoForbiddenAnnotations(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/noForbiddenAnnotations.php', [
			'forbiddenAnnotations' => ['@see', '@throws'],
		]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testForbiddenAnnotations(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/forbiddenAnnotations.php', [
			'forbiddenAnnotations' => ['@see', '@throws', '@Route'],
		]);

		$this->assertSame(7, $report->getErrorCount());

		$this->assertSniffError($report, 5, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		$this->assertSniffError($report, 6, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		$this->assertSniffError($report, 19, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		$this->assertSniffError($report, 21, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		$this->assertSniffError($report, 30, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		$this->assertSniffError($report, 32, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
		$this->assertSniffError($report, 45, ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN);
	}

	public function testFixableForbiddenAnnotations(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/forbiddenAnnotations.php', [
			'forbiddenAnnotations' => ['@see', '@throws', '@Route'],
		], [ForbiddenAnnotationsSniff::CODE_ANNOTATION_FORBIDDEN]);
		$this->assertAllFixedInFile($report);
	}

}
