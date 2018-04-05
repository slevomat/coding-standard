<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

class InlineDocCommentDeclarationSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoInvalidInlineDocCommentDeclarations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noInvalidInlineDocCommentDeclarations.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testInvalidInlineDocCommentDeclarations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/invalidInlineDocCommentDeclarations.php');

		self::assertSame(7, $report->getErrorCount());

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

		self::assertAllFixedInFile($report);
	}

}
