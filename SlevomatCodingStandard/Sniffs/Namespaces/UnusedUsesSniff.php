<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UnusedUsesSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_UNUSED_USE = 'UnusedUse';
	const CODE_MISMATCHING_CASE = 'MismatchingCaseSensitivity';

	/** @var bool */
	public $searchAnnotations = false;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $openTagPointer)
	{
		$unusedNames = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);

		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$pointer = $referencedName->getStartPointer();
			$nameParts = NamespaceHelper::getNameParts($name);
			$nameAsReferencedInFile = $nameParts[0];
			$normalizedNameAsReferencedInFile = UseStatement::normalizedNameAsReferencedInFile($nameAsReferencedInFile);
			if (
				!NamespaceHelper::isFullyQualifiedName($name)
				&& isset($unusedNames[$normalizedNameAsReferencedInFile])
			) {
				if (!$referencedName->hasSameUseStatementType($unusedNames[$normalizedNameAsReferencedInFile])) {
					continue;
				}
				if ($unusedNames[$normalizedNameAsReferencedInFile]->getNameAsReferencedInFile() !== $nameAsReferencedInFile) {
					$phpcsFile->addError(sprintf(
						'Case of reference name %s and use statement %s do not match.',
						$nameAsReferencedInFile,
						$unusedNames[$normalizedNameAsReferencedInFile]->getNameAsReferencedInFile()
					), $pointer, self::CODE_MISMATCHING_CASE);
				}
				unset($unusedNames[$normalizedNameAsReferencedInFile]);
			}
		}

		if ($this->searchAnnotations) {
			$tokens = $phpcsFile->getTokens();
			$searchAnnotationsPointer = $openTagPointer + 1;
			while (true) {
				$phpDocTokenPointer = $phpcsFile->findNext([T_DOC_COMMENT_TAG, T_DOC_COMMENT_STRING], $searchAnnotationsPointer);
				if ($phpDocTokenPointer === false) {
					break;
				}

				foreach ($unusedNames as $i => $useStatement) {
					if ($tokens[$phpDocTokenPointer]['code'] === T_DOC_COMMENT_TAG && preg_match('~^@' . preg_quote($useStatement->getNameAsReferencedInFile(), '~') . '(?:[^a-z\\d]|$)~i', $tokens[$phpDocTokenPointer]['content'])) {
						unset($unusedNames[$i]);
					} elseif ($tokens[$phpDocTokenPointer]['code'] === T_DOC_COMMENT_STRING && preg_match('~(?:^|[^a-z\\d\\\\])' . preg_quote($useStatement->getNameAsReferencedInFile(), '~') . '(?:[^a-z\\d\\\\]|$)~i', $tokens[$phpDocTokenPointer]['content'])) {
						unset($unusedNames[$i]);
					}
				}

				$searchAnnotationsPointer = $phpDocTokenPointer + 1;
			}
		}

		foreach ($unusedNames as $value) {
			$fullName = $value->getFullyQualifiedTypeName();
			if ($value->getNameAsReferencedInFile() !== $fullName && $value->getNameAsReferencedInFile() !== NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($fullName)) {
				$fullName .= sprintf(' (as %s)', $value->getNameAsReferencedInFile());
			}
			$fix = $phpcsFile->addFixableError(sprintf(
				'Type %s is not used in this file.',
				$fullName
			), $value->getPointer(), self::CODE_UNUSED_USE);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$endPointer = $phpcsFile->findNext(T_SEMICOLON, $value->getPointer()) + 1;
				for ($i = $value->getPointer(); $i <= $endPointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
