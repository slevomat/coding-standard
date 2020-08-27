<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class NamespaceDeclarationSniffTest extends TestCase
{

	public function testInvalidWhitespaceAfterNamespaceNoErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/namespaceDeclarationInvalidWhitespaceAfterNamespaceNoErrors.php',
			[],
			[NamespaceDeclarationSniff::CODE_INVALID_WHITESPACE_AFTER_NAMESPACE]
		);
		self::assertNoSniffErrorInFile($report);
	}

	public function testInvalidWhitespaceAfterNamespaceErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/namespaceDeclarationInvalidWhitespaceAfterNamespaceErrors.php',
			[],
			[NamespaceDeclarationSniff::CODE_INVALID_WHITESPACE_AFTER_NAMESPACE]
		);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			3,
			NamespaceDeclarationSniff::CODE_INVALID_WHITESPACE_AFTER_NAMESPACE,
			'Expected one space after namespace statement, found 5.'
		);
		self::assertSniffError(
			$report,
			5,
			NamespaceDeclarationSniff::CODE_INVALID_WHITESPACE_AFTER_NAMESPACE,
			'Expected one space after namespace statement, found tab.'
		);
		self::assertSniffError(
			$report,
			7,
			NamespaceDeclarationSniff::CODE_INVALID_WHITESPACE_AFTER_NAMESPACE,
			'Expected one space after namespace statement.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testDisallowedContentBetweenNamespaceNameAndSemicolonNoErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/namespaceDeclarationDisallowedContentBetweenNamespaceNameAndSemicolonNoErrors.php',
			[],
			[NamespaceDeclarationSniff::CODE_DISALLOWED_CONTENT_BETWEEN_NAMESPACE_NAME_AND_SEMICOLON]
		);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDisallowedContentBetweenNamespaceNameAndSemicolonErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/namespaceDeclarationDisallowedContentBetweenNamespaceNameAndSemicolonErrors.php',
			[],
			[NamespaceDeclarationSniff::CODE_DISALLOWED_CONTENT_BETWEEN_NAMESPACE_NAME_AND_SEMICOLON]
		);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, NamespaceDeclarationSniff::CODE_DISALLOWED_CONTENT_BETWEEN_NAMESPACE_NAME_AND_SEMICOLON);
		self::assertSniffError($report, 5, NamespaceDeclarationSniff::CODE_DISALLOWED_CONTENT_BETWEEN_NAMESPACE_NAME_AND_SEMICOLON);
		self::assertSniffError($report, 7, NamespaceDeclarationSniff::CODE_DISALLOWED_CONTENT_BETWEEN_NAMESPACE_NAME_AND_SEMICOLON);

		self::assertAllFixedInFile($report);
	}

	public function testDisallowedBracketedSyntaxNoErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/namespaceDeclarationDisallowedBracketedSyntaxNoErrors.php',
			[],
			[NamespaceDeclarationSniff::CODE_DISALLOWED_BRACKETED_SYNTAX]
		);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDisallowedBracketedSyntaxErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/namespaceDeclarationDisallowedBracketedSyntaxErrors.php',
			[],
			[NamespaceDeclarationSniff::CODE_DISALLOWED_BRACKETED_SYNTAX]
		);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, NamespaceDeclarationSniff::CODE_DISALLOWED_BRACKETED_SYNTAX);
		self::assertSniffError($report, 7, NamespaceDeclarationSniff::CODE_DISALLOWED_BRACKETED_SYNTAX);

		self::assertAllFixedInFile($report);
	}

}
