<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Parser\TokenIterator;

/**
 * @internal
 */
class ParsedDocComment
{

	/** @var int */
	private $openPointer;

	/** @var int */
	private $closePointer;

	/** @var PhpDocNode */
	private $node;

	/** @var TokenIterator */
	private $tokens;

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

}
