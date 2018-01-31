<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

class ForbiddenCommentsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoForbiddenComments(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/noForbiddenComments.php', [
			'forbiddenCommentPatterns' => ['~Foo\d+~', '~Not comment\.~'],
		]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testForbiddenComments(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/forbiddenComments.php', [
			'forbiddenCommentPatterns' => ['~Created by PhpStorm\.~', '~(\S+\s+)?Constructor\.~', '~(blah){3}~'],
		]);

		$this->assertSame(3, $report->getErrorCount());

		$this->assertSniffError($report, 4, ForbiddenCommentsSniff::CODE_COMMENT_FORBIDDEN);
		$this->assertSniffError($report, 10, ForbiddenCommentsSniff::CODE_COMMENT_FORBIDDEN);
		$this->assertSniffError($report, 36, ForbiddenCommentsSniff::CODE_COMMENT_FORBIDDEN);
	}

}
