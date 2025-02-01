<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\Attribute;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use function array_merge;
use function count;
use function strlen;
use function trim;
use const T_DOC_COMMENT_STRING;

/**
 * @internal
 */
class ParsedDocComment
{

	private int $openPointer;

	private int $closePointer;

	private PhpDocNode $node;

	private TokenIterator $tokens;

	public function __construct(int $openPointer, int $closePointer, PhpDocNode $node, TokenIterator $tokens)
	{
		$this->openPointer = $openPointer;
		$this->closePointer = $closePointer;
		$this->node = $node;
		$this->tokens = $tokens;
	}

	public function getOpenPointer(): int
	{
		return $this->openPointer;
	}

	public function getClosePointer(): int
	{
		return $this->closePointer;
	}

	public function getNode(): PhpDocNode
	{
		return $this->node;
	}

	public function getTokens(): TokenIterator
	{
		return $this->tokens;
	}

	public function getNodeStartPointer(File $phpcsFile, Node $node): int
	{
		$tokens = $phpcsFile->getTokens();

		$tagStartLine = $tokens[$this->openPointer]['line'] + $node->getAttribute('startLine') - 1;

		$searchPointer = $this->openPointer + 1;
		for ($i = $this->openPointer + 1; $i < $this->closePointer; $i++) {
			if ($tagStartLine === $tokens[$i]['line']) {
				$searchPointer = $i;
				break;
			}
		}

		return TokenHelper::findNext($phpcsFile, array_merge(TokenHelper::$annotationTokenCodes, [T_DOC_COMMENT_STRING]), $searchPointer);
	}

	public function getNodeEndPointer(File $phpcsFile, Node $node, int $nodeStartPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$content = trim($this->tokens->getContentBetween(
			$node->getAttribute(Attribute::START_INDEX),
			$node->getAttribute(Attribute::END_INDEX) + 1,
		));
		$length = strlen($content);

		$searchPointer = $nodeStartPointer;

		$content = '';
		for ($i = $nodeStartPointer; $i < count($tokens); $i++) {
			$content .= $tokens[$i]['content'];

			if (strlen($content) >= $length) {
				$searchPointer = $i;
				break;
			}
		}

		return TokenHelper::findPrevious(
			$phpcsFile,
			array_merge(TokenHelper::$annotationTokenCodes, [T_DOC_COMMENT_STRING]),
			$searchPointer,
		);
	}

}
