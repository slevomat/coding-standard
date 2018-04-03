<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\TokenHelper;

class NamespaceDeclarationSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_INVALID_WHITESPACE_AFTER_NAMESPACE = 'InvalidWhitespaceAfterNamespace';
	public const CODE_DISALLOWED_CONTENT_BETWEEN_NAMESPACE_NAME_AND_SEMICOLON = 'DisallowedContentBetweenNamespaceNameAndSemicolon';
	public const CODE_DISALLOWED_BRACKETED_SYNTAX = 'DisallowedBracketedSyntax';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_NAMESPACE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $namespacePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $namespacePointer): void
	{
		$this->checkWhitespaceAfterNamespace($phpcsFile, $namespacePointer);
		$this->checkDisallowedContentBetweenNamespaceNameAndSemicolon($phpcsFile, $namespacePointer);
		$this->checkDisallowedBracketedSyntax($phpcsFile, $namespacePointer);
	}

	private function checkWhitespaceAfterNamespace(\PHP_CodeSniffer\Files\File $phpcsFile, int $namespacePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$whitespacePointer = $namespacePointer + 1;

		if ($tokens[$whitespacePointer]['code'] !== T_WHITESPACE) {
			$phpcsFile->addError(
				'Expected one space after namespace statement.',
				$namespacePointer,
				self::CODE_INVALID_WHITESPACE_AFTER_NAMESPACE
			);
			return;
		}

		if ($tokens[$whitespacePointer]['content'] === ' ') {
			return;
		}

		if ($tokens[$whitespacePointer]['content'][0] === "\t") {
			$errorMessage = 'Expected one space after namespace statement, found tab.';
		} else {
			$errorMessage = sprintf(
				'Expected one space after namespace statement, found %d.',
				strlen($tokens[$whitespacePointer]['content'])
			);
		}

		$fix = $phpcsFile->addFixableError(
			$errorMessage,
			$namespacePointer,
			self::CODE_INVALID_WHITESPACE_AFTER_NAMESPACE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($whitespacePointer, ' ');
		$phpcsFile->fixer->endChangeset();
	}

	private function checkDisallowedContentBetweenNamespaceNameAndSemicolon(\PHP_CodeSniffer\Files\File $phpcsFile, int $namespacePointer): void
	{
		if (array_key_exists('scope_opener', $phpcsFile->getTokens()[$namespacePointer])) {
			return;
		}

		$namespaceNameStartPointer = TokenHelper::findNextEffective($phpcsFile, $namespacePointer + 1);
		$namespaceNameEndPointer = TokenHelper::findNextExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $namespaceNameStartPointer + 1) - 1;

		/** @var int $namespaceSemicolonPointer */
		$namespaceSemicolonPointer = TokenHelper::findNextLocal($phpcsFile, T_SEMICOLON, $namespaceNameEndPointer + 1);

		if ($namespaceNameEndPointer + 1 === $namespaceSemicolonPointer) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Disallowed content between namespace name and semicolon.',
			$namespacePointer,
			self::CODE_DISALLOWED_CONTENT_BETWEEN_NAMESPACE_NAME_AND_SEMICOLON
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		for ($i = $namespaceNameEndPointer + 1; $i < $namespaceSemicolonPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function checkDisallowedBracketedSyntax(\PHP_CodeSniffer\Files\File $phpcsFile, int $namespacePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (!array_key_exists('scope_opener', $tokens[$namespacePointer])) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Bracketed syntax for namespaces is disallowed.',
			$namespacePointer,
			self::CODE_DISALLOWED_BRACKETED_SYNTAX
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($tokens[$namespacePointer]['scope_opener'], ';');
		$phpcsFile->fixer->replaceToken($tokens[$namespacePointer]['scope_closer'], '');
		$phpcsFile->fixer->endChangeset();
	}

}
