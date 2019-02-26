<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedName;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_diff_key;
use function array_key_exists;
use function array_merge;
use function count;
use function in_array;
use function preg_match;
use function preg_quote;
use function preg_split;
use function sprintf;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_NAMESPACE;
use const T_OPEN_TAG;
use const T_SEMICOLON;

class UnusedUsesSniff implements Sniff
{

	public const CODE_UNUSED_USE = 'UnusedUse';
	public const CODE_MISMATCHING_CASE = 'MismatchingCaseSensitivity';

	/** @var bool */
	public $searchAnnotations = false;

	/** @var string[] */
	public $ignoredAnnotationNames = [];

	/** @var string[] */
	public $ignoredAnnotations = [];

	/** @var string[]|null */
	private $normalizedIgnoredAnnotationNames;

	/** @var string[]|null */
	private $normalizedIgnoredAnnotations;

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @return string[]
	 */
	private function getIgnoredAnnotationNames(): array
	{
		if ($this->normalizedIgnoredAnnotationNames === null) {
			$this->normalizedIgnoredAnnotationNames = array_merge(
				SniffSettingsHelper::normalizeArray($this->ignoredAnnotationNames),
				[
					'@param',
					'@throws',
					'@property',
					'@method',
				]
			);
		}

		return $this->normalizedIgnoredAnnotationNames;
	}

	/**
	 * @return string[]
	 */
	private function getIgnoredAnnotations(): array
	{
		if ($this->normalizedIgnoredAnnotations === null) {
			$this->normalizedIgnoredAnnotations = SniffSettingsHelper::normalizeArray($this->ignoredAnnotations);
		}

		return $this->normalizedIgnoredAnnotations;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		$fileUnusedNames = UseStatementHelper::getFileUseStatements($phpcsFile);
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);

		$allUsedNames = [];
		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$pointer = $referencedName->getStartPointer();
			$nameParts = NamespaceHelper::getNameParts($name);
			$nameAsReferencedInFile = $nameParts[0];
			$nameReferencedWithoutSubNamespace = count($nameParts) === 1;

			/** @var int $pointerBeforeUseStatements */
			$pointerBeforeUseStatements = TokenHelper::findPrevious($phpcsFile, [T_OPEN_TAG, T_NAMESPACE], $pointer - 1);

			$uniqueId = $nameReferencedWithoutSubNamespace
				? UseStatement::getUniqueId($referencedName->getType(), $nameAsReferencedInFile)
				: UseStatement::getUniqueId(ReferencedName::TYPE_DEFAULT, $nameAsReferencedInFile);
			if (
				NamespaceHelper::isFullyQualifiedName($name)
				|| !array_key_exists($pointerBeforeUseStatements, $fileUnusedNames)
				|| !array_key_exists($uniqueId, $fileUnusedNames[$pointerBeforeUseStatements])
			) {
				continue;
			}

			if ($fileUnusedNames[$pointerBeforeUseStatements][$uniqueId]->getNameAsReferencedInFile() !== $nameAsReferencedInFile) {
				$phpcsFile->addError(sprintf(
					'Case of reference name "%s" and use statement "%s" does not match.',
					$nameAsReferencedInFile,
					$fileUnusedNames[$pointerBeforeUseStatements][$uniqueId]->getNameAsReferencedInFile()
				), $pointer, self::CODE_MISMATCHING_CASE);
			}

			$allUsedNames[$pointerBeforeUseStatements][$uniqueId] = true;
		}

		if ($this->searchAnnotations) {
			$tokens = $phpcsFile->getTokens();
			$searchAnnotationsPointer = $openTagPointer + 1;
			while (true) {
				$docCommentOpenPointer = TokenHelper::findNext($phpcsFile, T_DOC_COMMENT_OPEN_TAG, $searchAnnotationsPointer);
				if ($docCommentOpenPointer === null) {
					break;
				}

				$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

				if (count($annotations) === 0) {
					$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
					continue;
				}

				/** @var int $pointerBeforeUseStatements */
				$pointerBeforeUseStatements = TokenHelper::findPrevious($phpcsFile, [T_OPEN_TAG, T_NAMESPACE], $docCommentOpenPointer - 1);

				if (!array_key_exists($pointerBeforeUseStatements, $fileUnusedNames)) {
					$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
					continue;
				}

				foreach ($fileUnusedNames[$pointerBeforeUseStatements] as $useStatement) {
					$nameAsReferencedInFile = $useStatement->getNameAsReferencedInFile();
					$uniqueId = UseStatement::getUniqueId($useStatement->getType(), $nameAsReferencedInFile);

					/** @var string $annotationName */
					foreach ($annotations as $annotationName => $annotationsByName) {
						if (in_array($annotationName, $this->getIgnoredAnnotations(), true)) {
							continue;
						}

						if (
							!in_array($annotationName, $this->getIgnoredAnnotationNames(), true)
							&& preg_match('~^@(' . preg_quote($nameAsReferencedInFile, '~') . ')(?=[^-a-z\\d]|$)~i', $annotationName, $matches) !== 0
						) {
							$allUsedNames[$pointerBeforeUseStatements][$uniqueId] = true;

							if ($matches[1] !== $nameAsReferencedInFile) {
								foreach ($annotationsByName as $annotation) {
									$phpcsFile->addError(sprintf(
										'Case of reference name "%s" and use statement "%s" does not match.',
										$matches[1],
										$fileUnusedNames[$pointerBeforeUseStatements][$uniqueId]->getNameAsReferencedInFile()
									), $annotation->getStartPointer(), self::CODE_MISMATCHING_CASE);
								}
							}
						}

						foreach ($annotationsByName as $annotation) {
							if (!$annotation instanceof GenericAnnotation) {
								continue;
							}

							if ($annotation->getParameters() === null) {
								continue;
							}

							if (
								preg_match('~(?<=^|[^a-z\\\\])(' . preg_quote($nameAsReferencedInFile, '~') . ')(?=::)~i', $annotation->getParameters(), $matches) === 0
								&& preg_match('~(?<=@)(' . preg_quote($nameAsReferencedInFile, '~') . ')(?=[^\\w])~i', $annotation->getParameters(), $matches) === 0
							) {
								continue;
							}

							$allUsedNames[$pointerBeforeUseStatements][$uniqueId] = true;

							if ($matches[1] === $nameAsReferencedInFile) {
								continue;
							}

							$phpcsFile->addError(sprintf(
								'Case of reference name "%s" and use statement "%s" does not match.',
								$matches[1],
								$fileUnusedNames[$pointerBeforeUseStatements][$uniqueId]->getNameAsReferencedInFile()
							), $annotation->getStartPointer(), self::CODE_MISMATCHING_CASE);
						}

						/** @var \SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ThrowsAnnotation|\SlevomatCodingStandard\Helpers\Annotation\PropertyAnnotation|\SlevomatCodingStandard\Helpers\Annotation\MethodAnnotation|\SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation $annotation */
						foreach ($annotationsByName as $annotation) {
							if ($annotation->getContent() === null) {
								continue;
							}

							if ($annotation->isInvalid()) {
								continue;
							}

							$content = $annotation->getContent();

							$contentsToCheck = [];
							if (!$annotation instanceof GenericAnnotation) {
								foreach (AnnotationHelper::getAnnotationTypes($annotation) as $annotationType) {
									foreach (AnnotationTypeHelper::getIdentifierTypeNodes($annotationType) as $typeNode) {
										if (!$typeNode instanceof IdentifierTypeNode) {
											continue;
										}

										if (
											TypeHintHelper::isSimpleTypeHint($typeNode->name)
											|| TypeHintHelper::isSimpleUnofficialTypeHints($typeNode->name)
											|| !TypeHelper::isTypeName($typeNode->name)
										) {
											continue;
										}

										$contentsToCheck[] = $typeNode->name;
									}
								}
							} elseif ($annotationName === '@see') {
								$contentsToCheck[] = preg_split('~(\\s+|::)~', $content)[0];
							} else {
								$contentsToCheck[] = $content;
							}

							foreach ($contentsToCheck as $contentToCheck) {
								if (preg_match('~(?<=^|\|)(' . preg_quote($nameAsReferencedInFile, '~') . ')(?=\\s|\\\\|\||\[|$)~i', $contentToCheck, $matches) === 0) {
									continue;
								}

								$allUsedNames[$pointerBeforeUseStatements][$uniqueId] = true;

								if ($matches[1] === $nameAsReferencedInFile) {
									continue;
								}

								$phpcsFile->addError(sprintf(
									'Case of reference name "%s" and use statement "%s" does not match.',
									$matches[1],
									$fileUnusedNames[$pointerBeforeUseStatements][$uniqueId]->getNameAsReferencedInFile()
								), $annotation->getStartPointer(), self::CODE_MISMATCHING_CASE);
							}
						}
					}
				}

				$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
			}
		}

		foreach ($fileUnusedNames as $pointerBeforeUnusedNames => $unusedNames) {
			$usedNames = $allUsedNames[$pointerBeforeUnusedNames] ?? [];
			foreach (array_diff_key($unusedNames, $usedNames) as $unusedUse) {
				$fullName = $unusedUse->getFullyQualifiedTypeName();
				if ($unusedUse->getNameAsReferencedInFile() !== $fullName && $unusedUse->getNameAsReferencedInFile() !== NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($fullName)) {
					$fullName .= sprintf(' (as %s)', $unusedUse->getNameAsReferencedInFile());
				}
				$fix = $phpcsFile->addFixableError(sprintf(
					'Type %s is not used in this file.',
					$fullName
				), $unusedUse->getPointer(), self::CODE_UNUSED_USE);
				if (!$fix) {
					continue;
				}

				$phpcsFile->fixer->beginChangeset();
				$endPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $unusedUse->getPointer()) + 1;
				for ($i = $unusedUse->getPointer(); $i <= $endPointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
