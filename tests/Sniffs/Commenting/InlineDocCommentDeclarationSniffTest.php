<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

class InlineDocCommentDeclarationSniffTest extends TestCase
{

	public function testNoInvalidInlineDocCommentDeclarations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noInvalidInlineDocCommentDeclarations.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testInvalidInlineDocCommentDeclarations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/invalidInlineDocCommentDeclarations.php');

		self::assertSame(16, $report->getErrorCount());

		self::assertSniffError(
			$report,
			11,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline documentation comment format "@var $a string[]", expected "@var string[] $a".'
		);
		self::assertSniffError(
			$report,
			17,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline documentation comment format "@var $c", expected "@var type $variable".'
		);

		self::assertSniffError(
			$report,
			20,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline documentation comment format "@var $d iterable|array|\Traversable Lorem ipsum", expected "@var iterable|array|\Traversable $d Lorem ipsum".'
		);

		self::assertSniffError(
			$report,
			23,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline documentation comment format "@var $f string", expected "@var string $f".'
		);

		self::assertSniffError(
			$report,
			28,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline documentation comment format "@var $h \DateTimeImmutable", expected "@var \DateTimeImmutable $h".'
		);

		self::assertSniffError(
			$report,
			33,
			InlineDocCommentDeclarationSniff::CODE_INVALID_COMMENT_TYPE
		);
		self::assertSniffError(
			$report,
			33,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT
		);

		self::assertSniffError(
			$report,
			36,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT
		);

		self::assertSniffError($report, 53, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 56, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 59, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 62, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 65, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 68, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 71, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 74, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);

		self::assertAllFixedInFile($report);
	}

}
