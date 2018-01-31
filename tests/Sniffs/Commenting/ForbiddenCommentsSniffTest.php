<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

class ForbiddenCommentsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testThrowExceptionForInvalidPattern(): void
	{
		$this->expectException(\Throwable::class);

		self::checkFile(
			__DIR__ . '/data/noForbiddenComments.php',
			['forbiddenCommentPatterns' => ['invalidPattern']]
		);
	}

	public function testNoForbiddenComments(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noForbiddenComments.php', [
			'forbiddenCommentPatterns' => ['~Foo\d+~', '~Not comment\.~'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testForbiddenComments(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenComments.php', [
			'forbiddenCommentPatterns' => ['~Created by PhpStorm\.~', '~(\S+\s+)?Constructor\.~', '~(blah){3}~'],
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 4, ForbiddenCommentsSniff::CODE_COMMENT_FORBIDDEN);
		self::assertSniffError($report, 10, ForbiddenCommentsSniff::CODE_COMMENT_FORBIDDEN);
		self::assertSniffError($report, 36, ForbiddenCommentsSniff::CODE_COMMENT_FORBIDDEN);
	}

}
