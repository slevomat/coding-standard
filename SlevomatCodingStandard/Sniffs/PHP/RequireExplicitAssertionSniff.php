<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_merge;
use function array_reverse;
use function array_unique;
use function count;
use function implode;
use function in_array;
use function sprintf;
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

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $docCommentOpenPointer
	 */
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$commentClosePointer = $tokens[$docCommentOpenPointer]['comment_closer'];

		$pointerAfterDocComment = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $commentClosePointer + 1);
		if ($pointerAfterDocComment === null || !in_array($tokens[$pointerAfterDocComment]['code'], [T_VARIABLE, T_FOREACH, T_WHILE, T_LIST, T_OPEN_SHORT_ARRAY], true)) {
			return;
		}

		$variableAnnotations = AnnotationHelper::getAnnotationsByName($phpcsFile, $docCommentOpenPointer, '@var');
		if (count($variableAnnotations) === 0) {
			return;
		}

		/** @var \SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation $variableAnnotation */
		foreach (array_reverse($variableAnnotations) as $variableAnnotation) {
			if ($variableAnnotation->getVariableName() === null) {
				continue;
			}

			$variableAnnotationType = $variableAnnotation->getType();

			if ($variableAnnotationType instanceof UnionTypeNode || $variableAnnotationType instanceof IntersectionTypeNode) {
				foreach ($variableAnnotationType->types as $typeNode) {
					if (!$this->isValidTypeNode($typeNode)) {
						continue 2;
					}
				}
			} elseif (!$this->isValidTypeNode($variableAnnotationType)) {
				continue;
			}

			if ($tokens[$pointerAfterDocComment]['code'] === T_VARIABLE) {
				$pointerAfterVariable = TokenHelper::findNextEffective($phpcsFile, $pointerAfterDocComment + 1);
				if ($tokens[$pointerAfterVariable]['code'] !== T_EQUAL) {
					continue;
				}

				if ($variableAnnotation->getVariableName() !== $tokens[$pointerAfterDocComment]['content']) {
					continue;
				}

				$pointerToAddAssertion = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $pointerAfterDocComment + 1);
				$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenPointer);

			} elseif ($tokens[$pointerAfterDocComment]['code'] === T_LIST) {
				$listParenthesisOpener = TokenHelper::findNextEffective($phpcsFile, $pointerAfterDocComment + 1);

				$variablePointerInList = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableAnnotation->getVariableName(), $listParenthesisOpener + 1, $tokens[$listParenthesisOpener]['parenthesis_closer']);
				if ($variablePointerInList === null) {
					continue;
				}

				$pointerToAddAssertion = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $pointerAfterDocComment + 1);
				$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenPointer);

			} elseif ($tokens[$pointerAfterDocComment]['code'] === T_OPEN_SHORT_ARRAY) {
				$pointerAfterList = TokenHelper::findNextEffective($phpcsFile, $tokens[$pointerAfterDocComment]['bracket_closer'] + 1);
				if ($tokens[$pointerAfterList]['code'] !== T_EQUAL) {
					continue;
				}

				$variablePointerInList = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableAnnotation->getVariableName(), $pointerAfterDocComment + 1, $tokens[$pointerAfterDocComment]['bracket_closer']);
				if ($variablePointerInList === null) {
					continue;
				}

				$pointerToAddAssertion = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $tokens[$pointerAfterDocComment]['bracket_closer'] + 1);
				$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenPointer);

			} else {
				if ($tokens[$pointerAfterDocComment]['code'] === T_WHILE) {
					$variablePointerInWhile = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableAnnotation->getVariableName(), $tokens[$pointerAfterDocComment]['parenthesis_opener'] + 1, $tokens[$pointerAfterDocComment]['parenthesis_closer']);
					if ($variablePointerInWhile === null) {
						continue;
					}

					$pointerAfterVariableInWhile = TokenHelper::findNextEffective($phpcsFile, $variablePointerInWhile + 1);
					if ($tokens[$pointerAfterVariableInWhile]['code'] !== T_EQUAL) {
						continue;
					}
				} else {
					$asPointer = TokenHelper::findNext($phpcsFile, T_AS, $tokens[$pointerAfterDocComment]['parenthesis_opener'] + 1, $tokens[$pointerAfterDocComment]['parenthesis_closer']);
					$variablePointerInForeach = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableAnnotation->getVariableName(), $asPointer + 1, $tokens[$pointerAfterDocComment]['parenthesis_closer']);
					if ($variablePointerInForeach === null) {
						continue;
					}
				}

				$pointerToAddAssertion = $tokens[$pointerAfterDocComment]['scope_opener'];
				$indentation = IndentationHelper::addIndentation(IndentationHelper::getIndentation($phpcsFile, $docCommentOpenPointer));
			}

			$fix = $phpcsFile->addFixableError('Use assertion instead of inline documentation comment.', $variableAnnotation->getStartPointer(), self::CODE_REQUIRED_EXPLICIT_ASSERTION);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			for ($i = $variableAnnotation->getStartPointer(); $i <= $variableAnnotation->getEndPointer(); $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

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

			if (!$docCommentUseful) {
				/** @var int $nextPointerAfterDocComment */
				$nextPointerAfterDocComment = TokenHelper::findNextEffective($phpcsFile, $docCommentClosePointer + 1);
				for ($i = $docCommentOpenPointer; $i < $nextPointerAfterDocComment; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
			}

			/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\UnionTypeNode $variableAnnotationType */
			$variableAnnotationType = $variableAnnotationType;

			$phpcsFile->fixer->addContent(
				$pointerToAddAssertion,
				$phpcsFile->eolChar . $indentation . $this->createAssert($variableAnnotation->getVariableName(), $variableAnnotationType)
			);
			$phpcsFile->fixer->endChangeset();
		}
	}

	private function isValidTypeNode(TypeNode $typeNode): bool
	{
		if ($typeNode instanceof ThisTypeNode) {
			return true;
		}

		if (!$typeNode instanceof IdentifierTypeNode) {
			return false;
		}

		return !in_array($typeNode->name, ['mixed', 'static'], true);
	}

	/**
	 * @param string $variableName
	 * @param \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode $typeNode
	 * @return string
	 */
	private function createAssert(string $variableName, TypeNode $typeNode): string
	{
		$conditions = [];

		if ($typeNode instanceof IdentifierTypeNode || $typeNode instanceof ThisTypeNode) {
			$conditions = $this->createConditions($variableName, $typeNode);
		} else {
			/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $innerTypeNode */
			foreach ($typeNode->types as $innerTypeNode) {
				$conditions = array_merge($conditions, $this->createConditions($variableName, $innerTypeNode));
			}
		}

		$operator = $typeNode instanceof IntersectionTypeNode ? '&&' : '||';

		return sprintf('\assert(%s);', implode(sprintf(' %s ', $operator), array_unique($conditions)));
	}

	/**
	 * @param string $variableName
	 * @param \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $typeNode
	 * @return string[]
	 */
	private function createConditions(string $variableName, TypeNode $typeNode): array
	{
		if ($typeNode instanceof ThisTypeNode) {
			return [sprintf('%s instanceof $this', $variableName)];
		}

		if ($typeNode->name === 'self') {
			return [sprintf('%s instanceof %s', $variableName, $typeNode->name)];
		}

		if (TypeHintHelper::isSimpleTypeHint($typeNode->name)) {
			return [sprintf('\is_%s(%s)', $typeNode->name, $variableName)];
		}

		if (in_array($typeNode->name, ['resource', 'object'], true)) {
			return [sprintf('\is_%s(%s)', $typeNode->name, $variableName)];
		}

		if (in_array($typeNode->name, ['true', 'false', 'null'], true)) {
			return [sprintf('%s === %s', $variableName, $typeNode->name)];
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

		return [sprintf('%s instanceof %s', $variableName, $typeNode->name)];
	}

}
