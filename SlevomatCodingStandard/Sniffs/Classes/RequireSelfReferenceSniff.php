<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_merge;
use function preg_quote;
use function preg_replace;
use const T_ANON_CLASS;
use const T_ATTRIBUTE;
use const T_OPEN_TAG;

class RequireSelfReferenceSniff implements Sniff
{

	public const CODE_REQUIRED_SELF_REFERENCE = 'RequiredSelfReference';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		if (TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $openTagPointer - 1) !== null) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$referencedNames = array_merge(
			ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer),
			ReferencedNameHelper::getAllReferencedNamesInAttributes($phpcsFile, $openTagPointer)
		);

		foreach ($referencedNames as $referencedName) {
			if (!$referencedName->isClass()) {
				continue;
			}

			$anonymousClassPointer = TokenHelper::findPrevious($phpcsFile, T_ANON_CLASS, $referencedName->getStartPointer() - 1);

			if (
				$anonymousClassPointer !== null
				&& $tokens[$anonymousClassPointer]['scope_closer'] > $referencedName->getEndPointer()
			) {
				continue;
			}

			$classPointer = ClassHelper::getClassPointer($phpcsFile, $referencedName->getStartPointer());
			if ($classPointer === null) {
				continue;
			}

			$className = ClassHelper::getFullyQualifiedName($phpcsFile, $classPointer);

			$resolvedName = NamespaceHelper::resolveClassName(
				$phpcsFile,
				$referencedName->getNameAsReferencedInFile(),
				$referencedName->getStartPointer()
			);

			if ($className !== $resolvedName) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				'"self" for local reference is required.',
				$referencedName->getStartPointer(),
				self::CODE_REQUIRED_SELF_REFERENCE
			);
			if (!$fix) {
				continue;
			}

			$inAttribute = $tokens[$referencedName->getStartPointer()]['code'] === T_ATTRIBUTE;

			$phpcsFile->fixer->beginChangeset();

			if ($inAttribute) {
				$attributeContent = TokenHelper::getContent(
					$phpcsFile,
					$referencedName->getStartPointer(),
					$referencedName->getEndPointer()
				);
				$fixedAttributeContent = preg_replace(
					'~(?<=\W)' . preg_quote($referencedName->getNameAsReferencedInFile(), '~') . '(?=\W)~',
					'self',
					$attributeContent
				);
				$phpcsFile->fixer->replaceToken($referencedName->getStartPointer(), $fixedAttributeContent);

			} else {
				$phpcsFile->fixer->replaceToken($referencedName->getStartPointer(), 'self');
			}

			FixerHelper::removeBetweenIncluding($phpcsFile, $referencedName->getStartPointer() + 1, $referencedName->getEndPointer());

			$phpcsFile->fixer->endChangeset();
		}
	}

}
