<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Attribute;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\ParsedDocComment;
use SlevomatCodingStandard\Helpers\PhpDocParserHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHint;
use function array_unshift;
use function count;
use function in_array;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strpos;
use function strtolower;
use function substr;
use function substr_count;
use function trim;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_TYPE_CLOSE_PARENTHESIS;
use const T_TYPE_INTERSECTION;
use const T_TYPE_OPEN_PARENTHESIS;
use const T_TYPE_UNION;
use const T_VARIABLE;
use const T_WHITESPACE;

class DNFTypeHintFormatSniff implements Sniff
{

	public const CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR = 'DisallowedWhitespaceAroundOperator';
	public const CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR = 'RequiredWhitespaceAroundOperator';
	public const CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES = 'DisallowedWhitespaceInsideParentheses';
	public const CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES = 'RequiredWhitespaceInsideParentheses';
	public const CODE_REQUIRED_SHORT_NULLABLE = 'RequiredShortNullable';
	public const CODE_DISALLOWED_SHORT_NULLABLE = 'DisallowedShortNullable';
	public const CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION = 'NullTypeHintNotOnFirstPosition';
	public const CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION = 'NullTypeHintNotOnLastPosition';

	private const YES = 'yes';
	private const NO = 'no';

	private const FIRST = 'first';
	private const LAST = 'last';

	public ?bool $enable = null;

	public bool $enableForDocComments = false;

	public ?string $withSpacesAroundOperators = null;

	public ?string $withSpacesInsideParentheses = null;

	public ?string $shortNullable = null;

	public ?string $nullPosition = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_VARIABLE,
			...TokenHelper::FUNCTION_TOKEN_CODES,
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	public function process(File $phpcsFile, int $pointer): void
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 80000);

		if (!$this->enable) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_DOC_COMMENT_OPEN_TAG) {
			if (!$this->enableForDocComments) {
				return;
			}

			$this->processDocComment($phpcsFile, $pointer);
			return;
		}

		if ($tokens[$pointer]['code'] === T_VARIABLE) {
			if (!PropertyHelper::isProperty($phpcsFile, $pointer)) {
				return;
			}

			$propertyTypeHint = PropertyHelper::findTypeHint($phpcsFile, $pointer);
			if ($propertyTypeHint !== null) {
				$this->checkTypeHint($phpcsFile, $propertyTypeHint);
			}

			return;
		}

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $pointer);
		if ($returnTypeHint !== null) {
			$this->checkTypeHint($phpcsFile, $returnTypeHint);
		}

		foreach (FunctionHelper::getParametersTypeHints($phpcsFile, $pointer) as $parameterTypeHint) {
			if ($parameterTypeHint !== null) {
				$this->checkTypeHint($phpcsFile, $parameterTypeHint);
			}
		}
	}

	private function processDocComment(File $phpcsFile, int $docCommentOpenPointer): void
	{
		$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);
		if ($parsedDocComment === null) {
			return;
		}

		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

		foreach ($annotations as $annotation) {
			if ($annotation->isInvalid()) {
				continue;
			}

			if ($this->withSpacesAroundOperators !== null) {
				foreach (AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), UnionTypeNode::class) as $unionTypeNode) {
					$this->checkDocCommentOperatorSpacing($phpcsFile, $annotation, $unionTypeNode, $parsedDocComment, '|');
				}

				foreach (AnnotationHelper::getAnnotationNodesByType(
					$annotation->getNode(),
					IntersectionTypeNode::class,
				) as $intersectionTypeNode) {
					$this->checkDocCommentOperatorSpacing($phpcsFile, $annotation, $intersectionTypeNode, $parsedDocComment, '&');
				}
			}

			if ($this->withSpacesInsideParentheses !== null) {
				foreach (AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), UnionTypeNode::class) as $unionTypeNode) {
					foreach ($unionTypeNode->types as $typeNode) {
						if (!$typeNode instanceof IntersectionTypeNode) {
							continue;
						}

						$this->checkDocCommentParenthesesSpacing($phpcsFile, $annotation, $typeNode, $parsedDocComment);
					}
				}
			}

			if ($this->nullPosition !== null) {
				foreach (AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), UnionTypeNode::class) as $unionTypeNode) {
					$this->checkDocCommentNullPosition($phpcsFile, $annotation, $unionTypeNode, $parsedDocComment);
				}
			}

			if ($this->shortNullable === null) {
				continue;
			}

			if ($this->shortNullable === self::NO) {
				foreach (AnnotationHelper::getAnnotationNodesByType(
					$annotation->getNode(),
					NullableTypeNode::class,
				) as $nullableTypeNode) {
					$this->checkDocCommentNullableExpansion($phpcsFile, $annotation, $nullableTypeNode, $parsedDocComment);
				}
			} elseif ($this->shortNullable === self::YES) {
				$this->checkDocCommentNullableContraction($phpcsFile, $annotation, $parsedDocComment);
			}
		}
	}

	/**
	 * @param UnionTypeNode|IntersectionTypeNode $typeNode
	 */
	private function checkDocCommentOperatorSpacing(
		File $phpcsFile,
		Annotation $annotation,
		TypeNode $typeNode,
		ParsedDocComment $parsedDocComment,
		string $operator
	): void
	{
		$rawTypeText = trim($parsedDocComment->getTokens()->getContentBetween(
			$typeNode->getAttribute(Attribute::START_INDEX),
			$typeNode->getAttribute(Attribute::END_INDEX) + 1,
		));

		$escapedOperator = preg_quote($operator, '~');

		if ($this->withSpacesAroundOperators === self::NO) {
			if (preg_match('~\s' . $escapedOperator . '|' . $escapedOperator . '\s~', $rawTypeText) === 0) {
				return;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('Spaces around "|" or "&" in type hint "%s" are disallowed.', $rawTypeText),
				$annotation->getStartPointer(),
				self::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
			);

			if ($fix) {
				$fixedTypeText = (string) preg_replace('~\s*' . $escapedOperator . '\s*~', $operator, $rawTypeText);
				$this->fixDocCommentTypeText($phpcsFile, $parsedDocComment, $rawTypeText, $fixedTypeText);
			}
		} elseif ($this->withSpacesAroundOperators === self::YES) {
			if (preg_match('~(?<! )' . $escapedOperator . '|' . $escapedOperator . '(?! )~', $rawTypeText) === 0) {
				return;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('One space required around each "|" or "&" in type hint "%s".', $rawTypeText),
				$annotation->getStartPointer(),
				self::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
			);

			if ($fix) {
				$fixedTypeText = (string) preg_replace('~\s*' . $escapedOperator . '\s*~', ' ' . $operator . ' ', $rawTypeText);
				$this->fixDocCommentTypeText($phpcsFile, $parsedDocComment, $rawTypeText, $fixedTypeText);
			}
		}
	}

	private function checkDocCommentParenthesesSpacing(
		File $phpcsFile,
		Annotation $annotation,
		IntersectionTypeNode $intersectionTypeNode,
		ParsedDocComment $parsedDocComment
	): void
	{
		$rawTypeText = trim($parsedDocComment->getTokens()->getContentBetween(
			$intersectionTypeNode->getAttribute(Attribute::START_INDEX),
			$intersectionTypeNode->getAttribute(Attribute::END_INDEX) + 1,
		));

		if ($this->withSpacesInsideParentheses === self::NO) {
			if (preg_match('~^\(\s|\s\)$~', $rawTypeText) === 0) {
				return;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('Spaces inside parentheses in type hint "%s" are disallowed.', $rawTypeText),
				$annotation->getStartPointer(),
				self::CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES,
			);

			if ($fix) {
				$fixedTypeText = (string) preg_replace(['~\(\s+~', '~\s+\)~'], ['(', ')'], $rawTypeText);
				$this->fixDocCommentTypeText($phpcsFile, $parsedDocComment, $rawTypeText, $fixedTypeText);
			}
		} elseif ($this->withSpacesInsideParentheses === self::YES) {
			if (preg_match('~^\([^ ]|[^ ]\)$~', $rawTypeText) === 0) {
				return;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('One space required around expression inside parentheses in type hint "%s".', $rawTypeText),
				$annotation->getStartPointer(),
				self::CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES,
			);

			if ($fix) {
				$fixedTypeText = (string) preg_replace(['~^\(\s*~', '~\s*\)$~'], ['( ', ' )'], $rawTypeText);
				$this->fixDocCommentTypeText($phpcsFile, $parsedDocComment, $rawTypeText, $fixedTypeText);
			}
		}
	}

	private function checkDocCommentNullPosition(
		File $phpcsFile,
		Annotation $annotation,
		UnionTypeNode $unionTypeNode,
		ParsedDocComment $parsedDocComment
	): void
	{
		$nullTypeNode = null;
		$nullPosition = 0;
		$position = 0;

		foreach ($unionTypeNode->types as $typeNode) {
			if ($typeNode instanceof IdentifierTypeNode && strtolower($typeNode->name) === 'null') {
				$nullTypeNode = $typeNode;
				$nullPosition = $position;
				break;
			}

			$position++;
		}

		if ($nullTypeNode === null) {
			return;
		}

		if ($this->nullPosition === self::LAST && $nullPosition === count($unionTypeNode->types) - 1) {
			return;
		}

		if ($this->nullPosition === self::FIRST && $nullPosition === 0) {
			return;
		}

		$errorCode = $this->nullPosition === self::LAST
			? self::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION
			: self::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION;

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Null type hint should be on %s position in "%s".',
				$this->nullPosition,
				AnnotationTypeHelper::print($unionTypeNode),
			),
			$annotation->getStartPointer(),
			$errorCode,
		);

		if (!$fix) {
			return;
		}

		$fixedTypeNodes = [];
		foreach ($unionTypeNode->types as $typeNode) {
			if ($typeNode === $nullTypeNode) {
				continue;
			}

			$fixedTypeNodes[] = $typeNode;
		}

		if ($this->nullPosition === self::FIRST) {
			array_unshift($fixedTypeNodes, $nullTypeNode);
		} else {
			$fixedTypeNodes[] = $nullTypeNode;
		}

		$fixedUnionTypeNode = PhpDocParserHelper::cloneNode($unionTypeNode);
		$fixedUnionTypeNode->types = $fixedTypeNodes;

		$phpcsFile->fixer->beginChangeset();

		$fixedDocComment = AnnotationHelper::fixAnnotation($parsedDocComment, $annotation, $unionTypeNode, $fixedUnionTypeNode);

		FixerHelper::change(
			$phpcsFile,
			$parsedDocComment->getOpenPointer(),
			$parsedDocComment->getClosePointer(),
			$fixedDocComment,
		);

		$phpcsFile->fixer->endChangeset();
	}

	private function checkDocCommentNullableExpansion(
		File $phpcsFile,
		Annotation $annotation,
		NullableTypeNode $nullableTypeNode,
		ParsedDocComment $parsedDocComment
	): void
	{
		$rawTypeText = trim($parsedDocComment->getTokens()->getContentBetween(
			$nullableTypeNode->getAttribute(Attribute::START_INDEX),
			$nullableTypeNode->getAttribute(Attribute::END_INDEX) + 1,
		));

		$fix = $phpcsFile->addFixableError(
			sprintf('Usage of short nullable type hint in "%s" is disallowed.', $rawTypeText),
			$annotation->getStartPointer(),
			self::CODE_DISALLOWED_SHORT_NULLABLE,
		);

		if (!$fix) {
			return;
		}

		$innerTypeText = AnnotationTypeHelper::print($nullableTypeNode->type);
		$separator = $this->withSpacesAroundOperators === self::YES ? ' | ' : '|';
		$fixedTypeText = $this->nullPosition === self::FIRST
			? 'null' . $separator . $innerTypeText
			: $innerTypeText . $separator . 'null';

		$this->fixDocCommentTypeText($phpcsFile, $parsedDocComment, $rawTypeText, $fixedTypeText);
	}

	private function checkDocCommentNullableContraction(File $phpcsFile, Annotation $annotation, ParsedDocComment $parsedDocComment): void
	{
		$directType = $this->getAnnotationDirectType($annotation);

		if (!$directType instanceof UnionTypeNode || count($directType->types) !== 2) {
			return;
		}

		$nullTypeNode = null;
		$otherTypeNode = null;

		foreach ($directType->types as $typeNode) {
			if ($typeNode instanceof IdentifierTypeNode && strtolower($typeNode->name) === 'null') {
				$nullTypeNode = $typeNode;
			} else {
				$otherTypeNode = $typeNode;
			}
		}

		if ($nullTypeNode === null || $otherTypeNode === null || $otherTypeNode instanceof IntersectionTypeNode) {
			return;
		}

		$rawTypeText = trim($parsedDocComment->getTokens()->getContentBetween(
			$directType->getAttribute(Attribute::START_INDEX),
			$directType->getAttribute(Attribute::END_INDEX) + 1,
		));

		$fix = $phpcsFile->addFixableError(
			sprintf('Short nullable type hint in "%s" is required.', $rawTypeText),
			$annotation->getStartPointer(),
			self::CODE_REQUIRED_SHORT_NULLABLE,
		);

		if (!$fix) {
			return;
		}

		$fixedTypeText = '?' . AnnotationTypeHelper::print($otherTypeNode);
		$this->fixDocCommentTypeText($phpcsFile, $parsedDocComment, $rawTypeText, $fixedTypeText);
	}

	private function fixDocCommentTypeText(
		File $phpcsFile,
		ParsedDocComment $parsedDocComment,
		string $originalText,
		string $fixedText
	): void
	{
		$rawDocblockContent = TokenHelper::getContent(
			$phpcsFile,
			$parsedDocComment->getOpenPointer(),
			$parsedDocComment->getClosePointer(),
		);

		$fixedDocblockContent = str_replace($originalText, $fixedText, $rawDocblockContent);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change(
			$phpcsFile,
			$parsedDocComment->getOpenPointer(),
			$parsedDocComment->getClosePointer(),
			$fixedDocblockContent,
		);

		$phpcsFile->fixer->endChangeset();
	}

	private function getAnnotationDirectType(Annotation $annotation): ?TypeNode
	{
		$value = $annotation->getValue();

		if (
			$value instanceof ParamTagValueNode
			|| $value instanceof ReturnTagValueNode
			|| $value instanceof VarTagValueNode
			|| $value instanceof PropertyTagValueNode
		) {
			return $value->type;
		}

		return null;
	}

	private function checkTypeHint(File $phpcsFile, TypeHint $typeHint): void
	{
		$tokens = $phpcsFile->getTokens();

		$typeHintsCount = substr_count($typeHint->getTypeHint(), '|') + substr_count($typeHint->getTypeHint(), '&') + 1;

		if ($typeHintsCount > 1) {
			if ($this->withSpacesAroundOperators === self::NO) {
				$error = false;
				foreach (TokenHelper::findNextAll(
					$phpcsFile,
					T_WHITESPACE,
					$typeHint->getStartPointer(),
					$typeHint->getEndPointer(),
				) as $whitespacePointer) {
					if (in_array($tokens[$whitespacePointer - 1]['code'], [T_TYPE_UNION, T_TYPE_INTERSECTION], true)) {
						$error = true;
						break;
					}
					if (in_array($tokens[$whitespacePointer + 1]['code'], [T_TYPE_UNION, T_TYPE_INTERSECTION], true)) {
						$error = true;
						break;
					}
				}

				if ($error) {
					$originalTypeHint = TokenHelper::getContent($phpcsFile, $typeHint->getStartPointer(), $typeHint->getEndPointer());
					$fix = $phpcsFile->addFixableError(
						sprintf('Spaces around "|" or "&" in type hint "%s" are disallowed.', $originalTypeHint),
						$typeHint->getStartPointer(),
						self::CODE_DISALLOWED_WHITESPACE_AROUND_OPERATOR,
					);
					if ($fix) {
						$fixedTypeHint = preg_replace('~\s*([|&])\s*~', '\1', $originalTypeHint);
						$this->fixTypeHint($phpcsFile, $typeHint, $fixedTypeHint);
					}
				}
			} elseif ($this->withSpacesAroundOperators === self::YES) {
				$error = false;
				foreach (TokenHelper::findNextAll(
					$phpcsFile,
					[T_TYPE_UNION, T_TYPE_INTERSECTION],
					$typeHint->getStartPointer(),
					$typeHint->getEndPointer(),
				) as $operatorPointer) {
					if ($tokens[$operatorPointer - 1]['content'] !== ' ') {
						$error = true;
						break;
					}
					if ($tokens[$operatorPointer + 1]['content'] !== ' ') {
						$error = true;
						break;
					}
				}

				if ($error) {
					$originalTypeHint = TokenHelper::getContent($phpcsFile, $typeHint->getStartPointer(), $typeHint->getEndPointer());
					$fix = $phpcsFile->addFixableError(
						sprintf('One space required around each "|" or "&" in type hint "%s".', $originalTypeHint),
						$typeHint->getStartPointer(),
						self::CODE_REQUIRED_WHITESPACE_AROUND_OPERATOR,
					);
					if ($fix) {
						$fixedTypeHint = preg_replace('~\s*([|&])\s*~', ' \1 ', $originalTypeHint);
						$this->fixTypeHint($phpcsFile, $typeHint, $fixedTypeHint);
					}
				}
			}

			if ($this->withSpacesInsideParentheses === self::NO) {
				$error = false;
				foreach (TokenHelper::findNextAll(
					$phpcsFile,
					T_WHITESPACE,
					$typeHint->getStartPointer(),
					$typeHint->getEndPointer(),
				) as $whitespacePointer) {
					if ($tokens[$whitespacePointer - 1]['code'] === T_TYPE_OPEN_PARENTHESIS) {
						$error = true;
						break;
					}
					if ($tokens[$whitespacePointer + 1]['code'] === T_TYPE_CLOSE_PARENTHESIS) {
						$error = true;
						break;
					}
				}

				if ($error) {
					$originalTypeHint = TokenHelper::getContent($phpcsFile, $typeHint->getStartPointer(), $typeHint->getEndPointer());
					$fix = $phpcsFile->addFixableError(
						sprintf('Spaces inside parentheses in type hint "%s" are disallowed.', $originalTypeHint),
						$typeHint->getStartPointer(),
						self::CODE_DISALLOWED_WHITESPACE_INSIDE_PARENTHESES,
					);
					if ($fix) {
						$fixedTypeHint = preg_replace('~\s+\)~', ')', preg_replace('~\(\s+~', '(', $originalTypeHint));
						$this->fixTypeHint($phpcsFile, $typeHint, $fixedTypeHint);
					}
				}
			} elseif ($this->withSpacesInsideParentheses === self::YES) {
				$error = false;
				foreach (TokenHelper::findNextAll(
					$phpcsFile,
					[T_TYPE_OPEN_PARENTHESIS, T_TYPE_CLOSE_PARENTHESIS],
					$typeHint->getStartPointer(),
					$typeHint->getEndPointer() + 1,
				) as $parenthesisPointer) {
					if (
						$tokens[$parenthesisPointer]['code'] === T_TYPE_OPEN_PARENTHESIS
						&& $tokens[$parenthesisPointer + 1]['content'] !== ' '
					) {
						$error = true;
						break;
					}
					if (
						$tokens[$parenthesisPointer]['code'] === T_TYPE_CLOSE_PARENTHESIS
						&& $tokens[$parenthesisPointer - 1]['content'] !== ' '
					) {
						$error = true;
						break;
					}
				}

				if ($error) {
					$originalTypeHint = TokenHelper::getContent($phpcsFile, $typeHint->getStartPointer(), $typeHint->getEndPointer());
					$fix = $phpcsFile->addFixableError(
						sprintf('One space required around expression inside parentheses in type hint "%s".', $originalTypeHint),
						$typeHint->getStartPointer(),
						self::CODE_REQUIRED_WHITESPACE_INSIDE_PARENTHESES,
					);
					if ($fix) {
						$fixedTypeHint = preg_replace('~\s*\)~', ' )', preg_replace('~\(\s*~', '( ', $originalTypeHint));
						$this->fixTypeHint($phpcsFile, $typeHint, $fixedTypeHint);
					}
				}
			}
		}

		if (substr_count($typeHint->getTypeHint(), '&') > 0) {
			return;
		}

		if (!$typeHint->isNullable()) {
			return;
		}

		$hasShortNullable = strpos($typeHint->getTypeHint(), '?') === 0;

		if ($this->shortNullable === self::YES && $typeHintsCount === 2 && !$hasShortNullable) {
			$fix = $phpcsFile->addFixableError(
				sprintf('Short nullable type hint in "%s" is required.', $typeHint->getTypeHint()),
				$typeHint->getStartPointer(),
				self::CODE_REQUIRED_SHORT_NULLABLE,
			);
			if ($fix) {
				$typeHintWithoutNull = self::getTypeHintContentWithoutNull($phpcsFile, $typeHint);
				$this->fixTypeHint($phpcsFile, $typeHint, '?' . $typeHintWithoutNull);
			}
		} elseif ($this->shortNullable === self::NO && $hasShortNullable) {
			$fix = $phpcsFile->addFixableError(
				sprintf('Usage of short nullable type hint in "%s" is disallowed.', $typeHint->getTypeHint()),
				$typeHint->getStartPointer(),
				self::CODE_DISALLOWED_SHORT_NULLABLE,
			);
			if ($fix) {
				$this->fixTypeHint($phpcsFile, $typeHint, substr($typeHint->getTypeHint(), 1) . '|null');
			}
		}

		if ($hasShortNullable || ($this->shortNullable === self::YES && $typeHintsCount === 2)) {
			return;
		}

		if ($this->nullPosition === self::FIRST && strtolower($tokens[$typeHint->getStartPointer()]['content']) !== 'null') {
			$fix = $phpcsFile->addFixableError(
				sprintf('Null type hint should be on first position in "%s".', $typeHint->getTypeHint()),
				$typeHint->getStartPointer(),
				self::CODE_NULL_TYPE_HINT_NOT_ON_FIRST_POSITION,
			);
			if ($fix) {
				$this->fixTypeHint($phpcsFile, $typeHint, 'null|' . self::getTypeHintContentWithoutNull($phpcsFile, $typeHint));
			}
		} elseif ($this->nullPosition === self::LAST && strtolower($tokens[$typeHint->getEndPointer()]['content']) !== 'null') {
			$fix = $phpcsFile->addFixableError(
				sprintf('Null type hint should be on last position in "%s".', $typeHint->getTypeHint()),
				$typeHint->getStartPointer(),
				self::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION,
			);
			if ($fix) {
				$this->fixTypeHint($phpcsFile, $typeHint, self::getTypeHintContentWithoutNull($phpcsFile, $typeHint) . '|null');
			}
		}
	}

	private function getTypeHintContentWithoutNull(File $phpcsFile, TypeHint $typeHint): string
	{
		$tokens = $phpcsFile->getTokens();

		if (strtolower($tokens[$typeHint->getEndPointer()]['content']) === 'null') {
			$previousTypeHintPointer = TokenHelper::findPrevious(
				$phpcsFile,
				TokenHelper::ONLY_TYPE_HINT_TOKEN_CODES,
				$typeHint->getEndPointer() - 1,
			);
			return TokenHelper::getContent($phpcsFile, $typeHint->getStartPointer(), $previousTypeHintPointer);
		}

		$content = '';

		for ($i = $typeHint->getStartPointer(); $i <= $typeHint->getEndPointer(); $i++) {
			if (strtolower($tokens[$i]['content']) === 'null') {
				$i = TokenHelper::findNext($phpcsFile, TokenHelper::ONLY_TYPE_HINT_TOKEN_CODES, $i + 1);
			}

			$content .= $tokens[$i]['content'];
		}

		return $content;
	}

	private function fixTypeHint(File $phpcsFile, TypeHint $typeHint, string $fixedTypeHint): void
	{
		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change($phpcsFile, $typeHint->getStartPointer(), $typeHint->getEndPointer(), $fixedTypeHint);

		$phpcsFile->fixer->endChangeset();
	}

}
