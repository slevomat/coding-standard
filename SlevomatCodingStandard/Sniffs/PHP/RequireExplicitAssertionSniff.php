<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_key_exists;
use function array_merge;
use function array_reverse;
use function array_unique;
use function count;
use function implode;
use function in_array;
use function sprintf;
use function strpos;
use function trim;
use const T_AS;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_EQUAL;
use const T_FOREACH;
use const T_LIST;
use const T_OPEN_SHORT_ARRAY;
use const T_SEMICOLON;
use const T_VARIABLE;
use const T_WHILE;
use const T_WHITESPACE;

class RequireExplicitAssertionSniff implements Sniff
{

	public const CODE_REQUIRED_EXPLICIT_ASSERTION = 'RequiredExplicitAssertion';

	/** @var bool */
	public $enableIntegerRanges = false;

	/** @var bool */
	public $enableAdvancedStringTypes = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $docCommentOpenPointer
	 */
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$tokenCodes = [T_VARIABLE, T_FOREACH, T_WHILE, T_LIST, T_OPEN_SHORT_ARRAY];
		$commentClosePointer = $tokens[$docCommentOpenPointer]['comment_closer'];

		$codePointer = TokenHelper::findFirstNonWhitespaceOnNextLine($phpcsFile, $commentClosePointer);

		if ($codePointer === null || !in_array($tokens[$codePointer]['code'], $tokenCodes, true)) {
			$firstPointerOnPreviousLine = TokenHelper::findFirstNonWhitespaceOnPreviousLine($phpcsFile, $docCommentOpenPointer);
			if (
				$firstPointerOnPreviousLine === null
				|| !in_array($tokens[$firstPointerOnPreviousLine]['code'], $tokenCodes, true)
			) {
				return;
			}

			$codePointer = $firstPointerOnPreviousLine;
		}

		/** @var list<Annotation<VarTagValueNode>> $variableAnnotations */
		$variableAnnotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer, '@var');
		if (count($variableAnnotations) === 0) {
			return;
		}

		foreach (array_reverse($variableAnnotations) as $variableAnnotation) {
			if ($variableAnnotation->isInvalid()) {
				continue;
			}

			$variableName = $variableAnnotation->getValue()->variableName;

			if ($variableName === '') {
				continue;
			}

			$variableAnnotationType = $variableAnnotation->getValue()->type;

			if (
				$variableAnnotationType instanceof UnionTypeNode
				|| $variableAnnotationType instanceof IntersectionTypeNode
			) {
				foreach ($variableAnnotationType->types as $typeNode) {
					if (!$this->isValidTypeNode($typeNode)) {
						continue 2;
					}
				}
			} elseif (!$this->isValidTypeNode($variableAnnotationType)) {
				continue;
			}

			/** @var IdentifierTypeNode|ThisTypeNode|UnionTypeNode|GenericTypeNode $variableAnnotationType */
			$variableAnnotationType = $variableAnnotationType;

			$assertion = $this->createAssert($variableName, $variableAnnotationType);

			if ($assertion === null) {
				continue;
			}

			if ($tokens[$codePointer]['code'] === T_VARIABLE) {
				$pointerAfterVariable = TokenHelper::findNextEffective($phpcsFile, $codePointer + 1);
				if ($tokens[$pointerAfterVariable]['code'] !== T_EQUAL) {
					continue;
				}

				if ($variableName !== $tokens[$codePointer]['content']) {
					continue;
				}

				$pointerToAddAssertion = $this->getNextSemicolonInSameScope($phpcsFile, $codePointer, $codePointer + 1);
				$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenPointer);

			} elseif ($tokens[$codePointer]['code'] === T_LIST) {
				$listParenthesisOpener = TokenHelper::findNextEffective($phpcsFile, $codePointer + 1);

				$variablePointerInList = TokenHelper::findNextContent(
					$phpcsFile,
					T_VARIABLE,
					$variableName,
					$listParenthesisOpener + 1,
					$tokens[$listParenthesisOpener]['parenthesis_closer']
				);
				if ($variablePointerInList === null) {
					continue;
				}

				$pointerToAddAssertion = $this->getNextSemicolonInSameScope($phpcsFile, $codePointer, $codePointer + 1);
				$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenPointer);

			} elseif ($tokens[$codePointer]['code'] === T_OPEN_SHORT_ARRAY) {
				$pointerAfterList = TokenHelper::findNextEffective($phpcsFile, $tokens[$codePointer]['bracket_closer'] + 1);
				if ($tokens[$pointerAfterList]['code'] !== T_EQUAL) {
					continue;
				}

				$variablePointerInList = TokenHelper::findNextContent(
					$phpcsFile,
					T_VARIABLE,
					$variableName,
					$codePointer + 1,
					$tokens[$codePointer]['bracket_closer']
				);
				if ($variablePointerInList === null) {
					continue;
				}

				$pointerToAddAssertion = $this->getNextSemicolonInSameScope(
					$phpcsFile,
					$codePointer,
					$tokens[$codePointer]['bracket_closer'] + 1
				);
				$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenPointer);

			} else {
				if ($tokens[$codePointer]['code'] === T_WHILE) {
					$variablePointerInWhile = TokenHelper::findNextContent(
						$phpcsFile,
						T_VARIABLE,
						$variableName,
						$tokens[$codePointer]['parenthesis_opener'] + 1,
						$tokens[$codePointer]['parenthesis_closer']
					);
					if ($variablePointerInWhile === null) {
						continue;
					}

					$pointerAfterVariableInWhile = TokenHelper::findNextEffective($phpcsFile, $variablePointerInWhile + 1);
					if ($tokens[$pointerAfterVariableInWhile]['code'] !== T_EQUAL) {
						continue;
					}
				} else {
					$asPointer = TokenHelper::findNext(
						$phpcsFile,
						T_AS,
						$tokens[$codePointer]['parenthesis_opener'] + 1,
						$tokens[$codePointer]['parenthesis_closer']
					);
					$variablePointerInForeach = TokenHelper::findNextContent(
						$phpcsFile,
						T_VARIABLE,
						$variableName,
						$asPointer + 1,
						$tokens[$codePointer]['parenthesis_closer']
					);
					if ($variablePointerInForeach === null) {
						continue;
					}
				}

				$pointerToAddAssertion = $tokens[$codePointer]['scope_opener'];
				$indentation = IndentationHelper::addIndentation(IndentationHelper::getIndentation($phpcsFile, $codePointer));
			}

			$fix = $phpcsFile->addFixableError(
				'Use assertion instead of inline documentation comment.',
				$variableAnnotation->getStartPointer(),
				self::CODE_REQUIRED_EXPLICIT_ASSERTION
			);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			FixerHelper::removeBetweenIncluding($phpcsFile, $variableAnnotation->getStartPointer(), $variableAnnotation->getEndPointer());

			$docCommentUseful = false;
			$docCommentClosePointer = $tokens[$docCommentOpenPointer]['comment_closer'];
			for ($i = $docCommentOpenPointer + 1; $i < $docCommentClosePointer; $i++) {
				$tokenContent = trim($phpcsFile->fixer->getTokenContent($i));
				if ($tokenContent === '' || $tokenContent === '*') {
					continue;
				}

				$docCommentUseful = true;
				break;
			}

			$pointerBeforeDocComment = TokenHelper::findPreviousContent(
				$phpcsFile,
				T_WHITESPACE,
				$phpcsFile->eolChar,
				$docCommentOpenPointer - 1
			);
			$pointerAfterDocComment = TokenHelper::findNextContent(
				$phpcsFile,
				T_WHITESPACE,
				$phpcsFile->eolChar,
				$docCommentClosePointer + 1
			);

			if (!$docCommentUseful) {
				FixerHelper::removeBetweenIncluding($phpcsFile, $pointerBeforeDocComment + 1, $pointerAfterDocComment);
			}

			if (
				$pointerToAddAssertion < $docCommentClosePointer
				&& array_key_exists($pointerAfterDocComment + 1, $tokens)
			) {
				$phpcsFile->fixer->addContentBefore($pointerAfterDocComment + 1, $indentation . $assertion . $phpcsFile->eolChar);
			} else {
				$phpcsFile->fixer->addContent($pointerToAddAssertion, $phpcsFile->eolChar . $indentation . $assertion);
			}

			$phpcsFile->fixer->endChangeset();
		}
	}

	private function isValidTypeNode(TypeNode $typeNode): bool
	{
		if ($typeNode instanceof ThisTypeNode) {
			return true;
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			return true;
		}

		if (
			$this->enableIntegerRanges
			&& $typeNode instanceof GenericTypeNode
			&& $typeNode->type->name === 'int'
			&& count($typeNode->genericTypes) === 2
		) {
			foreach ($typeNode->genericTypes as $genericType) {
				$isValid = ($genericType instanceof IdentifierTypeNode && in_array($genericType->name, ['min', 'max'], true))
					|| ($genericType instanceof ConstTypeNode && $genericType->constExpr instanceof ConstExprIntegerNode);

				if (!$isValid) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	private function getNextSemicolonInSameScope(File $phpcsFile, int $scopePointer, int $searchAt): int
	{
		$semicolonPointer = null;

		do {
			$semicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $searchAt);

			if (ScopeHelper::isInSameScope($phpcsFile, $scopePointer, $semicolonPointer)) {
				break;
			}

			$searchAt = $semicolonPointer + 1;

		} while (true);

		return $semicolonPointer;
	}

	/**
	 * @param IdentifierTypeNode|ThisTypeNode|UnionTypeNode|IntersectionTypeNode|GenericTypeNode $typeNode
	 */
	private function createAssert(string $variableName, TypeNode $typeNode): ?string
	{
		$conditions = [];

		if (
			$typeNode instanceof IdentifierTypeNode
			|| $typeNode instanceof ThisTypeNode
			|| $typeNode instanceof GenericTypeNode
		) {
			$conditions = $this->createConditions($variableName, $typeNode);

			return $conditions !== [] ? sprintf('\assert(%s);', implode(' || ', $conditions)) : null;
		}

		/** @var IdentifierTypeNode|ThisTypeNode|GenericTypeNode $innerTypeNode */
		foreach ($typeNode->types as $innerTypeNode) {
			$innerTypeConditions = $this->createConditions($variableName, $innerTypeNode);

			if ($innerTypeConditions === []) {
				return null;
			}

			$conditions = array_merge($conditions, $innerTypeConditions);
		}

		$operator = $typeNode instanceof IntersectionTypeNode ? '&&' : '||';

		$formattedConditions = [];

		foreach (array_unique($conditions) as $condition) {
			$formattedConditions[] = $operator === '||' && strpos($condition, '&&') !== false ? sprintf('(%s)', $condition) : $condition;
		}

		return sprintf('\assert(%s);', implode(sprintf(' %s ', $operator), $formattedConditions));
	}

	/**
	 * @param IdentifierTypeNode|ThisTypeNode|GenericTypeNode $typeNode
	 * @return list<string>
	 */
	private function createConditions(string $variableName, TypeNode $typeNode): array
	{
		if ($typeNode instanceof GenericTypeNode) {
			$conditions = [sprintf('\is_int(%s)', $variableName)];

			if ($typeNode->genericTypes[0] instanceof ConstTypeNode) {
				$conditions[] = sprintf('%s >= %s', $variableName, (string) $typeNode->genericTypes[0]);
			}

			if ($typeNode->genericTypes[1] instanceof ConstTypeNode) {
				$conditions[] = sprintf('%s <= %s', $variableName, (string) $typeNode->genericTypes[1]);
			}

			return [implode(' && ', $conditions)];
		}

		if ($typeNode instanceof ThisTypeNode) {
			return [sprintf('%s instanceof $this', $variableName)];
		}

		if ($typeNode->name === 'self') {
			return [sprintf('%s instanceof %s', $variableName, $typeNode->name)];
		}

		if ($typeNode->name === 'static') {
			return [sprintf('%s instanceof static', $variableName)];
		}

		if (in_array($typeNode->name, ['true', 'false', 'null'], true)) {
			return [sprintf('%s === %s', $variableName, $typeNode->name)];
		}

		if (
			$typeNode->name === 'mixed'
			|| TypeHintHelper::isVoidTypeHint($typeNode->name)
			|| TypeHintHelper::isNeverTypeHint($typeNode->name)
		) {
			return [];
		}

		if (TypeHintHelper::isSimpleTypeHint($typeNode->name)) {
			return [sprintf('\is_%s(%s)', TypeHintHelper::convertLongSimpleTypeHintToShort($typeNode->name), $variableName)];
		}

		if (in_array($typeNode->name, ['resource', 'object'], true)) {
			return [sprintf('\is_%s(%s)', $typeNode->name, $variableName)];
		}

		if ($typeNode->name === 'numeric') {
			return [
				sprintf('\is_int(%s)', $variableName),
				sprintf('\is_float(%s)', $variableName),
			];
		}

		if ($typeNode->name === 'scalar') {
			return [
				sprintf('\is_int(%s)', $variableName),
				sprintf('\is_float(%s)', $variableName),
				sprintf('\is_bool(%s)', $variableName),
				sprintf('\is_string(%s)', $variableName),
			];
		}

		if ($this->enableIntegerRanges) {
			if ($typeNode->name === 'positive-int') {
				return [sprintf('\is_int(%1$s) && %1$s > 0', $variableName)];
			}

			if ($typeNode->name === 'negative-int') {
				return [sprintf('\is_int(%1$s) && %1$s < 0', $variableName)];
			}
		}

		if (
			$this->enableAdvancedStringTypes
			&& in_array($typeNode->name, ['non-empty-string', 'non-falsy-string', 'callable-string', 'numeric-string'], true)
		) {
			$conditions = [sprintf('\is_string(%s)', $variableName)];

			if ($typeNode->name === 'non-empty-string') {
				$conditions[] = sprintf("%s !== ''", $variableName);
			} elseif ($typeNode->name === 'non-falsy-string') {
				$conditions[] = sprintf('(bool) %s === true', $variableName);
			} elseif ($typeNode->name === 'callable-string') {
				$conditions[] = sprintf('\is_callable(%s)', $variableName);
			} else {
				$conditions[] = sprintf('\is_numeric(%s)', $variableName);
			}

			return [implode(' && ', $conditions)];
		}

		if (TypeHintHelper::isSimpleUnofficialTypeHints($typeNode->name)) {
			return [];
		}

		return [sprintf('%s instanceof %s', $variableName, $typeNode->name)];
	}

}
