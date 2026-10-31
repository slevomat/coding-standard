<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Attribute;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use function assert;
use function in_array;
use function is_int;
use function rsort;
use function strlen;
use function substr_replace;
use const T_DOC_COMMENT_OPEN_TAG;

class RequireTrailingCommaInArrayShapeSniff implements Sniff
{

	public const CODE_MISSING_TRAILING_COMMA = 'MissingTrailingComma';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_DOC_COMMENT_OPEN_TAG];
	}

	public function process(File $phpcsFile, int $docCommentOpenPointer): void
	{
		$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);
		if ($parsedDocComment === null) {
			return;
		}

		$docTokens = $parsedDocComment->getTokens()->getTokens();
		$commaOffsets = [];
		foreach (AnnotationHelper::getAnnotationNodesByType($parsedDocComment->getNode(), ArrayShapeNode::class) as $shape) {
			$lastIndex = $this->findLastTokenIndex($docTokens, $shape);
			if ($lastIndex === null) {
				continue;
			}

			$commaOffsets[] = strlen($parsedDocComment->getTokens()->getContentBetween(0, $lastIndex + 1));
		}

		if ($commaOffsets === []) {
			return;
		}

		// Work backwards so insertions in the same PHPCS token do not shift later offsets.
		rsort($commaOffsets);
		$tokens = $phpcsFile->getTokens();
		$tokenEndOffset = 0;
		$tokenEndOffsets = [];
		for ($i = $docCommentOpenPointer; $i <= $parsedDocComment->getClosePointer(); $i++) {
			$tokenEndOffset += strlen($tokens[$i]['content']);
			$tokenEndOffsets[$i] = $tokenEndOffset;
		}

		$pointer = $parsedDocComment->getClosePointer();
		$replacements = [];
		foreach ($commaOffsets as $commaOffset) {
			while ($pointer > $docCommentOpenPointer && $tokenEndOffsets[$pointer - 1] >= $commaOffset) {
				$pointer--;
			}

			$fix = $phpcsFile->addFixableError(
				'Multi-line array shapes must have a trailing comma after the last element.',
				$pointer,
				self::CODE_MISSING_TRAILING_COMMA,
			);
			if (!$fix) {
				continue;
			}

			$offset = $commaOffset - $tokenEndOffsets[$pointer] + strlen($tokens[$pointer]['content']);
			$replacements[$pointer] = substr_replace($replacements[$pointer] ?? $tokens[$pointer]['content'], ',', $offset, 0);
		}

		$phpcsFile->fixer->beginChangeset();
		foreach ($replacements as $replacementPointer => $content) {
			FixerHelper::replace($phpcsFile, $replacementPointer, $content);
		}
		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param list<array{string, int, int}> $tokens
	 */
	private function findLastTokenIndex(array $tokens, ArrayShapeNode $shape): ?int
	{
		$startIndex = $shape->getAttribute(Attribute::START_INDEX);
		$endIndex = $shape->getAttribute(Attribute::END_INDEX);
		assert(is_int($startIndex));
		assert(is_int($endIndex));

		// Parenthesized shapes include their parentheses in the node's indexed range.
		while ($tokens[$startIndex][Lexer::TYPE_OFFSET] !== Lexer::TOKEN_OPEN_CURLY_BRACKET) {
			$startIndex++;
		}

		$depth = 0;
		for ($i = $startIndex; $i <= $endIndex; $i++) {
			if ($tokens[$i][Lexer::TYPE_OFFSET] === Lexer::TOKEN_OPEN_CURLY_BRACKET) {
				$depth++;
			} elseif ($tokens[$i][Lexer::TYPE_OFFSET] === Lexer::TOKEN_CLOSE_CURLY_BRACKET) {
				$depth--;
				if ($depth === 0) {
					$endIndex = $i;
					break;
				}
			}
		}

		if ($tokens[$startIndex][Lexer::LINE_OFFSET] === $tokens[$endIndex][Lexer::LINE_OFFSET]) {
			return null;
		}

		$lastIndex = $endIndex - 1;
		while (in_array(
			$tokens[$lastIndex][Lexer::TYPE_OFFSET],
			[Lexer::TOKEN_HORIZONTAL_WS, Lexer::TOKEN_PHPDOC_EOL, Lexer::TOKEN_COMMENT],
			true,
		)) {
			$lastIndex--;
		}

		return $lastIndex === $startIndex || $tokens[$lastIndex][Lexer::TYPE_OFFSET] === Lexer::TOKEN_COMMA ? null : $lastIndex;
	}

}
