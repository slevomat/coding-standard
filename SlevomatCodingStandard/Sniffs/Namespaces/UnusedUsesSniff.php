<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\MethodAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\PropertyAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ThrowsAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
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
use function array_merge;
use function count;
use function in_array;
use function preg_match;
use function preg_quote;
use function preg_split;
use function sprintf;
use const T_DOC_COMMENT_OPEN_TAG;
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
	 * @return mixed[]
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
		$unusedNames = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);

		$usedNames = [];
		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$pointer = $referencedName->getStartPointer();
			$nameParts = NamespaceHelper::getNameParts($name);
			$nameAsReferencedInFile = $nameParts[0];
			$nameReferencedWithoutSubNamespace = count($nameParts) === 1;
			$uniqueId = $nameReferencedWithoutSubNamespace
				? UseStatement::getUniqueId($referencedName->getType(), $nameAsReferencedInFile)
				: UseStatement::getUniqueId(ReferencedName::TYPE_DEFAULT, $nameAsReferencedInFile);
			if (
				NamespaceHelper::isFullyQualifiedName($name)
				|| !isset($unusedNames[$uniqueId])
			) {
				continue;
			}

			if ($unusedNames[$uniqueId]->getNameAsReferencedInFile() !== $nameAsReferencedInFile) {
				$phpcsFile->addError(sprintf(
					'Case of reference name "%s" and use statement "%s" does not match.',
					$nameAsReferencedInFile,
					$unusedNames[$uniqueId]->getNameAsReferencedInFile()
				), $pointer, self::CODE_MISMATCHING_CASE);
			}

			$usedNames[$uniqueId] = true;
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

				foreach ($unusedNames as $useStatement) {
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
							$usedNames[$uniqueId] = true;

							if ($matches[1] !== $nameAsReferencedInFile) {
								/** @var \SlevomatCodingStandard\Helpers\Annotation\Annotation $annotation */
								foreach ($annotationsByName as $annotation) {
									$phpcsFile->addError(sprintf(
										'Case of reference name "%s" and use statement "%s" does not match.',
										$matches[1],
										$unusedNames[$uniqueId]->getNameAsReferencedInFile()
									), $annotation->getStartPointer(), self::CODE_MISMATCHING_CASE);
								}
							}
						}

						/** @var \SlevomatCodingStandard\Helpers\Annotation\Annotation $annotation */
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

							$usedNames[$uniqueId] = true;

							if ($matches[1] === $nameAsReferencedInFile) {
								continue;
							}

							$phpcsFile->addError(sprintf(
								'Case of reference name "%s" and use statement "%s" does not match.',
								$matches[1],
								$unusedNames[$uniqueId]->getNameAsReferencedInFile()
							), $annotation->getStartPointer(), self::CODE_MISMATCHING_CASE);
						}

						/** @var \SlevomatCodingStandard\Helpers\Annotation\Annotation $annotation */
						foreach ($annotationsByName as $annotation) {
							if ($annotation->getContent() === null) {
								continue;
							}

							if ($annotation->isInvalid()) {
								continue;
							}

							$content = $annotation->getContent();

							$contentsToCheck = [];
							if ($annotation instanceof MethodAnnotation) {
								if ($annotation->getMethodReturnType() !== null) {
									$contentsToCheck = array_merge($contentsToCheck, $this->getAllTypesFromNode($annotation->getMethodReturnType()));
								}

								foreach ($annotation->getMethodParameters() as $methodParameter) {
									if ($methodParameter->type === null) {
										continue;
									}

									$contentsToCheck = array_merge($contentsToCheck, $this->getAllTypesFromNode($methodParameter->type));
								}
							} elseif (
								$annotation instanceof ParameterAnnotation
								|| $annotation instanceof ReturnAnnotation
								|| $annotation instanceof VariableAnnotation
								|| $annotation instanceof ThrowsAnnotation
								|| $annotation instanceof PropertyAnnotation
							) {
								$contentsToCheck = array_merge($contentsToCheck, $this->getAllTypesFromNode($annotation->getType()));
							} elseif ($annotationName === '@see') {
								$contentsToCheck[] = preg_split('~(\\s+|::)~', $content)[0];
							} else {
								$contentsToCheck[] = $content;
							}

							foreach ($contentsToCheck as $contentToCheck) {
								if (preg_match('~(?<=^|\|)(' . preg_quote($nameAsReferencedInFile, '~') . ')(?=\\s|\\\\|\||\[|$)~i', $contentToCheck, $matches) === 0) {
									continue;
								}

								$usedNames[$uniqueId] = true;

								if ($matches[1] === $nameAsReferencedInFile) {
									continue;
								}

								$phpcsFile->addError(sprintf(
									'Case of reference name "%s" and use statement "%s" does not match.',
									$matches[1],
									$unusedNames[$uniqueId]->getNameAsReferencedInFile()
								), $annotation->getStartPointer(), self::CODE_MISMATCHING_CASE);
							}
						}
					}
				}

				$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
			}
		}

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

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\TypeNode $typeNode
	 * @return string[]
	 */
	private function getAllTypesFromNode(TypeNode $typeNode): array
	{
		if ($typeNode instanceof UnionTypeNode) {
			$types = [];

			foreach ($typeNode->types as $node) {
				$types = array_merge($types, $this->getAllTypesFromNode($node));
			}

			return $types;
		}

		if ($typeNode instanceof ArrayTypeNode) {
			return $this->getAllTypesFromNode($typeNode->type);
		}

		if ($typeNode instanceof NullableTypeNode) {
			return $this->getAllTypesFromNode($typeNode->type);
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			if (
				!TypeHintHelper::isSimpleTypeHint($typeNode->name)
				&& !TypeHintHelper::isSimpleUnofficialTypeHints($typeNode->name)
				&& TypeHelper::isTypeName($typeNode->name)
			) {
				return [$typeNode->name];
			}
		}

		return [];
	}

}
