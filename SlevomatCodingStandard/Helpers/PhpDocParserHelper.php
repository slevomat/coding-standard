<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\NodeTraverser;
use PHPStan\PhpDocParser\Ast\NodeVisitor\CloningVisitor;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\Printer\Printer;

/**
 * @internal
 */
class PhpDocParserHelper
{

	public static function getLexer(): Lexer
	{
		static $lexer;

		if ($lexer === null) {
			$lexer = new Lexer(true);
		}

		return $lexer;
	}

	public static function getParser(): PhpDocParser
	{
		static $parser;

		if ($parser === null) {
			$usedAttributes = ['lines' => true, 'indexes' => true];

			$constantExpressionParser = new ConstExprParser(true, true, $usedAttributes);
			$parser = new PhpDocParser(
				new TypeParser($constantExpressionParser, true, $usedAttributes),
				$constantExpressionParser,
				true,
				true,
				$usedAttributes,
				true,
				true
			);
		}

		return $parser;
	}

	public static function getPrinter(): Printer
	{
		static $printer;

		if ($printer === null) {
			$printer = new Printer();
		}

		return $printer;
	}

	/**
	 * @template T of Node
	 * @param T $node
	 * @return T
	 */
	public static function cloneNode(Node $node): Node
	{
		static $cloningTraverser;

		if ($cloningTraverser === null) {
			$cloningTraverser = new NodeTraverser([new CloningVisitor()]);
		}

		[$cloneNode] = $cloningTraverser->traverse([$node]);

		return $cloneNode;
	}

}
