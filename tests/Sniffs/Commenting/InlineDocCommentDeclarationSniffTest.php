<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

class InlineDocCommentDeclarationSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoInvalidInlineDocCommentDeclarations(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/noInvalidInlineDocCommentDeclarations.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testInvalidInlineDocCommentDeclarations(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/invalidInlineDocCommentDeclarations.php');

		$this->assertSame(3, $report->getErrorCount());

		$this->assertSniffError(
			$report,
			12,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline doc comment format "@var $a string[]" for variable $a, expected "@var string[] $a".'
		);
		$this->assertSniffError(
			$report,
			18,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline doc comment format "@var $c" for variable $c, expected "@var type $c".'
		);
		$this->assertSniffError(
			$report,
			21,
			InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT,
			'Invalid inline doc comment format "@var $d iterable|array|\Traversable Lorem ipsum" for variable $d, expected "@var iterable|array|\Traversable $d Lorem ipsum".'
		);
	}

	public function testFixableInvalidInlineDocCommentDeclarations(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/invalidInlineDocCommentDeclarations.php', [], [InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT]);
		$this->assertAllFixedInFile($report);
	}

}
