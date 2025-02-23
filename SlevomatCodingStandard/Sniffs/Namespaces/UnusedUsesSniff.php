<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\Doctrine\DoctrineAnnotation;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
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
use function array_filter;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_reverse;
use function count;
use function in_array;
use function preg_match;
use function preg_quote;
use function sprintf;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_NAMESPACE;
use const T_OPEN_TAG;
use const T_SEMICOLON;

class UnusedUsesSniff implements Sniff
{

	public const CODE_UNUSED_USE = 'UnusedUse';

	public bool $searchAnnotations = false;

	/** @var list<string> */
	public array $ignoredAnnotationNames = [];

	/** @var list<string> */
	public array $ignoredAnnotations = [];

	/** @var list<string>|null */
	private ?array $normalizedIgnoredAnnotationNames = null;

	/** @var list<string>|null */
	private ?array $normalizedIgnoredAnnotations = null;

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

		$startPointer = TokenHelper::findPrevious($phpcsFile, T_NAMESPACE, $openTagPointer - 1) ?? $openTagPointer;

		$fileUnusedNames = UseStatementHelper::getFileUseStatements($phpcsFile);
		$referencedNamesInCode = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $startPointer);
		$referencedNamesInAttributes = ReferencedNameHelper::getAllReferencedNamesInAttributes($phpcsFile, $startPointer);

		$pointersBeforeUseStatements = array_reverse(NamespaceHelper::getAllNamespacesPointers($phpcsFile));

		$allUsedNames = [];

		foreach ([$referencedNamesInCode, $referencedNamesInAttributes] as $referencedNames) {
			foreach ($referencedNames as $referencedName) {
				$pointer = $referencedName->getStartPointer();

				$pointerBeforeUseStatements = $this->firstPointerBefore($pointer, $pointersBeforeUseStatements, $startPointer);

				$name = $referencedName->getNameAsReferencedInFile();
				$nameParts = NamespaceHelper::getNameParts($name);
				$nameAsReferencedInFile = $nameParts[0];
				$nameReferencedWithoutSubNamespace = count($nameParts) === 1;
				$uniqueId = $nameReferencedWithoutSubNamespace
					? UseStatement::getUniqueId($referencedName->getType(), $nameAsReferencedInFile)
					: UseStatement::getUniqueId(ReferencedName::TYPE_CLASS, $nameAsReferencedInFile);
				if (
					NamespaceHelper::isFullyQualifiedName($name)
					|| !array_key_exists($pointerBeforeUseStatements, $fileUnusedNames)
					|| !array_key_exists($uniqueId, $fileUnusedNames[$pointerBeforeUseStatements])
				) {
					continue;
				}

				$allUsedNames[$pointerBeforeUseStatements][$uniqueId] = true;
			}
		}

		if ($this->searchAnnotations) {
			$tokens = $phpcsFile->getTokens();
			$searchAnnotationsPointer = $startPointer + 1;
			while (true) {
				$docCommentOpenPointer = TokenHelper::findNext($phpcsFile, T_DOC_COMMENT_OPEN_TAG, $searchAnnotationsPointer);
				if ($docCommentOpenPointer === null) {
					break;
				}

				$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

				if ($annotations === []) {
					$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
					continue;
				}

				$pointerBeforeUseStatements = $this->firstPointerBefore(
					$docCommentOpenPointer - 1,
					$pointersBeforeUseStatements,
					$startPointer,
				);

				if (!array_key_exists($pointerBeforeUseStatements, $fileUnusedNames)) {
					$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
					continue;
				}

				foreach ($fileUnusedNames[$pointerBeforeUseStatements] as $useStatement) {
					if (!$useStatement->isClass()) {
						continue;
					}

					$nameAsReferencedInFile = $useStatement->getNameAsReferencedInFile();
					$uniqueId = UseStatement::getUniqueId($useStatement->getType(), $nameAsReferencedInFile);

					foreach ($annotations as $annotation) {
						if (in_array($annotation->getName(), $this->getIgnoredAnnotations(), true)) {
							continue;
						}

						if ($annotation->isInvalid()) {
							continue;
						}

						$contentsToCheck = [];

						if ($annotation->getValue() instanceof GenericTagValueNode) {
							$contentsToCheck[] = $annotation->getName();
							$contentsToCheck[] = $annotation->getValue()->value;
						} else {
							$identifierTypeNodes = AnnotationHelper::getAnnotationNodesByType(
								$annotation->getNode(),
								IdentifierTypeNode::class,
							);
							$doctrineAnnotations = AnnotationHelper::getAnnotationNodesByType(
								$annotation->getNode(),
								DoctrineAnnotation::class,
							);
							$constFetchNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), ConstFetchNode::class);

							$contentsToCheck = array_filter(array_merge(
								$contentsToCheck,
								array_map(static function (IdentifierTypeNode $identifierTypeNode): ?string {
									if (
										TypeHintHelper::isSimpleTypeHint($identifierTypeNode->name)
										|| TypeHintHelper::isSimpleUnofficialTypeHints($identifierTypeNode->name)
										|| !TypeHelper::isTypeName($identifierTypeNode->name)
									) {
										return null;
									}

									return $identifierTypeNode->name;
								}, $identifierTypeNodes),
								array_map(function (DoctrineAnnotation $doctrineAnnotation): ?string {
									if (in_array($doctrineAnnotation->name, $this->getIgnoredAnnotationNames(), true)) {
										return null;
									}

									return $doctrineAnnotation->name;
								}, $doctrineAnnotations),
								array_map(
									static fn (ConstFetchNode $constFetchNode): string => $constFetchNode->className,
									$constFetchNodes,
								),
							), static fn (?string $content): bool => $content !== null);
						}

						foreach ($contentsToCheck as $contentToCheck) {
							if (preg_match(
								'~(?<=^|[^a-z\\\\])(' . preg_quote($nameAsReferencedInFile, '~') . ')(?=\\s|::|\\\\|\||\[|$)~im',
								$contentToCheck,
							) === 0) {
								continue;
							}

							$allUsedNames[$pointerBeforeUseStatements][$uniqueId] = true;
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
				if (
					$unusedUse->getNameAsReferencedInFile() !== $fullName
					&& $unusedUse->getNameAsReferencedInFile() !== NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($fullName)
				) {
					$fullName .= sprintf(' (as %s)', $unusedUse->getNameAsReferencedInFile());
				}
				$fix = $phpcsFile->addFixableError(sprintf(
					'Type %s is not used in this file.',
					$fullName,
				), $unusedUse->getPointer(), self::CODE_UNUSED_USE);
				if (!$fix) {
					continue;
				}

				$endPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $unusedUse->getPointer()) + 1;

				$phpcsFile->fixer->beginChangeset();

				FixerHelper::removeBetweenIncluding($phpcsFile, $unusedUse->getPointer(), $endPointer);

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * @return list<string>
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
				],
			);
		}

		return $this->normalizedIgnoredAnnotationNames;
	}

	/**
	 * @return list<string>
	 */
	private function getIgnoredAnnotations(): array
	{
		if ($this->normalizedIgnoredAnnotations === null) {
			$this->normalizedIgnoredAnnotations = SniffSettingsHelper::normalizeArray($this->ignoredAnnotations);
		}

		return $this->normalizedIgnoredAnnotations;
	}

	/**
	 * @param list<int> $pointersBeforeUseStatements
	 */
	private function firstPointerBefore(int $pointer, array $pointersBeforeUseStatements, int $startPointer): int
	{
		foreach ($pointersBeforeUseStatements as $pointerBeforeUseStatements) {
			if ($pointerBeforeUseStatements < $pointer) {
				return $pointerBeforeUseStatements;
			}
		}

		return $startPointer;
	}

}
