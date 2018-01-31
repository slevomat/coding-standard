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

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			12,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline doc comment format "@var $a string[]" for variable $a, expected "@var string[] $a".'
		);
		self::assertSniffError(
			$report,
			18,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline doc comment format "@var $c" for variable $c, expected "@var type $c".'
		);
		self::assertSniffError(
			$report,
			21,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline doc comment format "@var $d iterable|array|\Traversable Lorem ipsum" for variable $d, expected "@var iterable|array|\Traversable $d Lorem ipsum".'
		);
	}

	public function testFixableInvalidInlineDocCommentDeclarations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/invalidInlineDocCommentDeclarations.php', [], [InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT]);
		self::assertAllFixedInFile($report);
	}

}
