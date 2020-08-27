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

		self::assertSame(36, $report->getErrorCount());

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

		self::assertSniffError($report, 33, InlineDocCommentDeclarationSniff::CODE_INVALID_COMMENT_TYPE);
		self::assertSniffError($report, 33, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);

		self::assertSniffError($report, 36, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);

		self::assertSniffError($report, 39, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 42, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);

		self::assertSniffError($report, 59, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 62, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 65, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 68, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 71, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 74, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 77, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 80, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 123, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);
		self::assertSniffError($report, 144, InlineDocCommentDeclarationSniff::CODE_INVALID_FORMAT);

		self::assertSniffError(
			$report,
			86,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknown before or after the documentation comment.'
		);

		self::assertSniffError(
			$report,
			88,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownA before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			91,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownB before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			94,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownC before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			97,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownD before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			101,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownE before or after the documentation comment.'
		);

		self::assertSniffError(
			$report,
			107,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownAA before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			110,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownBB before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			113,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownCC before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			116,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownDD before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			120,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownEE before or after the documentation comment.'
		);

		self::assertSniffError(
			$report,
			127,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownX before or after the documentation comment.'
		);

		self::assertSniffError(
			$report,
			129,
			InlineDocCommentDeclarationSniff::CODE_NO_ASSIGNMENT,
			'No assignment to $noAssignmentX variable before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			132,
			InlineDocCommentDeclarationSniff::CODE_NO_ASSIGNMENT,
			'No assignment to $noAssignmentY variable before or after the documentation comment.'
		);
		self::assertSniffError(
			$report,
			135,
			InlineDocCommentDeclarationSniff::CODE_NO_ASSIGNMENT,
			'No assignment to $noAssignmentZ variable before or after the documentation comment.'
		);

		self::assertSniffError(
			$report,
			140,
			InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE,
			'Missing variable $unknownParameter before or after the documentation comment.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testNoErrorsWithDocCommentAboveReturnAllowed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/inlineDocDocommentDeclarationWithDocCommentAboveReturnAllowedNoErrors.php', [
			'allowDocCommentAboveReturn' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsWithDocCommentAboveReturnAllowed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/inlineDocDocommentDeclarationWithDocCommentAboveReturnAllowedErrors.php', [
			'allowDocCommentAboveReturn' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, InlineDocCommentDeclarationSniff::CODE_INVALID_COMMENT_TYPE);

		self::assertAllFixedInFile($report);
	}

	public function testNoErrorsWithDocCommentAboveNonAssigmentAllowed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/inlineDocDocommentDeclarationWithDocCommentAboveNonAssigmentAllowedNoErrors.php', [
			'allowAboveNonAssignment' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsWithDocCommentAboveNonAssigmentAllowed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/inlineDocDocommentDeclarationWithDocCommentAboveNonAssigmentAllowedErrors.php', [
			'allowAboveNonAssignment' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 6, InlineDocCommentDeclarationSniff::CODE_MISSING_VARIABLE);
	}

}
