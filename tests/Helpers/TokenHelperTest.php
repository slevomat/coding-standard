<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function sprintf;
use const T_CLASS;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_EQUAL;
use const T_FOR;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_TAG;
use const T_SEMICOLON;
use const T_STRING;
use const T_VARIABLE;
use const T_WHITESPACE;

class TokenHelperTest extends TestCase
{

	public function testFindNextAll(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/useStatements.php');

		$tokens = $phpcsFile->getTokens();

		$pointers = TokenHelper::findNextAll($phpcsFile, [T_OPEN_TAG, T_CLASS, T_CLOSURE], 0);

		self::assertCount(5, $pointers);

		self::assertSame(T_OPEN_TAG, $tokens[$pointers[0]]['code']);
		self::assertSame(T_CLASS, $tokens[$pointers[1]]['code']);
		self::assertSame(T_CLOSURE, $tokens[$pointers[2]]['code']);
		self::assertSame(T_CLOSURE, $tokens[$pointers[3]]['code']);
		self::assertSame(T_CLASS, $tokens[$pointers[4]]['code']);
	}

	public function testFindNextEffective(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/emptyPhpFile.php');
		self::assertTokenPointer(T_OPEN_TAG, 1, $phpcsFile, TokenHelper::findNextEffective($phpcsFile, 0));
	}

	public function testFindNextEffectiveAtEndOfFile(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/emptyPhpFile.php');
		$openTagPointer = TokenHelper::findNext($phpcsFile, T_OPEN_TAG, 0);
		self::assertTokenPointer(T_OPEN_TAG, 1, $phpcsFile, $openTagPointer);
		self::assertNull(TokenHelper::findNextEffective($phpcsFile, $openTagPointer + 1));
	}

	public function testFindNextEffectiveWithComment(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/effectiveCodeWithComment.php');
		self::assertTokenPointer(T_CLASS, 5, $phpcsFile, TokenHelper::findNextEffective($phpcsFile, 1));
	}

	public function testFindNextEffectiveWithDocComment(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/effectiveCodeWithDocComment.php');
		self::assertTokenPointer(T_CLASS, 8, $phpcsFile, TokenHelper::findNextEffective($phpcsFile, 1));
	}

	public function testFindNothingNextExcluding(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		self::assertNull(TokenHelper::findNextExcluding($phpcsFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
			T_VARIABLE,
			T_EQUAL,
			T_STRING,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], 0));
	}

	public function testFindNextExcluding(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		self::assertTokenPointer(T_VARIABLE, 3, $phpcsFile, TokenHelper::findNextExcluding($phpcsFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
		], 0));
	}

	public function testFindNextExcludingWithSpecifiedEndPointer(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		$variableTokenPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, 0);
		self::assertTokenPointer(T_VARIABLE, 3, $phpcsFile, $variableTokenPointer);
		self::assertNull(TokenHelper::findNextExcluding($phpcsFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
		], 0, $variableTokenPointer));
	}

	public function testFindNextAnyToken(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		$variableTokenPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, 0);
		self::assertTokenPointer(T_VARIABLE, 3, $phpcsFile, $variableTokenPointer);
		self::assertTokenPointer(T_VARIABLE, 3, $phpcsFile, TokenHelper::findNextAnyToken($phpcsFile, $variableTokenPointer));
	}

	public function testFindPreviousEffective(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		$barTokenPointer = TokenHelper::findNext($phpcsFile, T_STRING, 1);
		$assignmentTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $barTokenPointer - 1);
		self::assertTokenPointer(T_EQUAL, 3, $phpcsFile, $assignmentTokenPointer);
	}

	public function testFindPreviousEffectiveWithComment(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/effectiveCodeWithComment.php');
		self::assertTokenPointer(
			T_OPEN_TAG,
			1,
			$phpcsFile,
			TokenHelper::findPreviousEffective($phpcsFile, TokenHelper::findNext($phpcsFile, T_CLASS, 0) - 1)
		);
	}

	public function testFindPreviousEffectiveWithDocComment(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/effectiveCodeWithDocComment.php');
		self::assertTokenPointer(
			T_OPEN_TAG,
			1,
			$phpcsFile,
			TokenHelper::findPreviousEffective($phpcsFile, TokenHelper::findNext($phpcsFile, T_CLASS, 0) - 1)
		);
	}

	public function testFindNothingPreviousExcluding(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		self::assertNull(TokenHelper::findPreviousExcluding($phpcsFile, [
			T_OPEN_TAG,
			T_WHITESPACE,
			T_VARIABLE,
			T_EQUAL,
			T_STRING,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], TokenHelper::getLastTokenPointer($phpcsFile)));
	}

	public function testFindPreviousExcluding(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		self::assertTokenPointer(T_OPEN_PARENTHESIS, 3, $phpcsFile, TokenHelper::findPreviousExcluding($phpcsFile, [
			T_WHITESPACE,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], TokenHelper::getLastTokenPointer($phpcsFile)));
	}

	public function testFindPreviousExcludingWithSpecifiedEndPointer(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');

		$lastTokenPointer = TokenHelper::getLastTokenPointer($phpcsFile);
		$stringTokenPointer = TokenHelper::findNext($phpcsFile, T_STRING, 0);
		self::assertTokenPointer(T_STRING, 3, $phpcsFile, $stringTokenPointer);
		self::assertTokenPointer(T_STRING, 3, $phpcsFile, TokenHelper::findPreviousExcluding($phpcsFile, [
			T_WHITESPACE,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], $lastTokenPointer, $stringTokenPointer));
	}

	public function testFindNothingPreviousExcludingWithSpecifiedEndPointer(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');

		$lastTokenPointer = TokenHelper::getLastTokenPointer($phpcsFile);
		$openParenthesisTokenPointer = TokenHelper::findNext($phpcsFile, T_OPEN_PARENTHESIS, 0);
		self::assertTokenPointer(T_OPEN_PARENTHESIS, 3, $phpcsFile, $openParenthesisTokenPointer);
		self::assertNull(TokenHelper::findPreviousExcluding($phpcsFile, [
			T_WHITESPACE,
			T_OPEN_PARENTHESIS,
			T_CLOSE_PARENTHESIS,
			T_SEMICOLON,
		], $lastTokenPointer, $openParenthesisTokenPointer));
	}

	public function testFindFirstTokenOnLine(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		$functionCallPointer = TokenHelper::findNext($phpcsFile, T_STRING, 0);
		self::assertTokenPointer(T_STRING, 3, $phpcsFile, $functionCallPointer);
		self::assertTokenPointer(T_VARIABLE, 3, $phpcsFile, TokenHelper::findFirstTokenOnLine($phpcsFile, $functionCallPointer));
	}

	public function testFindFirstTokenOnLineForFirstToken(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		$openTagPointer = TokenHelper::findNext($phpcsFile, T_OPEN_TAG, 0);
		self::assertTokenPointer(T_OPEN_TAG, 1, $phpcsFile, $openTagPointer);
		self::assertSame($openTagPointer, TokenHelper::findFirstTokenOnLine($phpcsFile, $openTagPointer));
	}

	public function testFindFirstTokenOnNextLine(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleTwo.php');
		$variableTokenPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, 0);
		self::assertTokenPointer(T_VARIABLE, 3, $phpcsFile, $variableTokenPointer);
		self::assertTokenPointer(T_STRING, 4, $phpcsFile, TokenHelper::findFirstTokenOnNextLine($phpcsFile, $variableTokenPointer));
	}

	public function testFindFirstNonWhitespaceOnLine(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleThree.php');
		self::assertSame(0, TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, 0));
	}

	public function testFindFirstNonWhitespaceOnIndentedLine(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/forLoop.php');
		$tokens = $phpcsFile->getTokens();

		$openParenthesisPointer = $this->findPointerByLineAndType($phpcsFile, 4, T_OPEN_PARENTHESIS);
		self::assertNotNull($openParenthesisPointer);

		$firstNonWhiteSpaceTokenPointer = TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $openParenthesisPointer);
		self::assertTokenPointer(T_STRING, 4, $phpcsFile, $firstNonWhiteSpaceTokenPointer);
		self::assertSame('foo', $tokens[$firstNonWhiteSpaceTokenPointer]['content']);
	}

	public function testFindFirstTokenOnNextLineEndingWithAComment(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleFour.php');
		$variableTokenPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, 0);
		self::assertTokenPointer(T_VARIABLE, 3, $phpcsFile, $variableTokenPointer);
		self::assertTokenPointer(T_STRING, 4, $phpcsFile, TokenHelper::findFirstTokenOnNextLine($phpcsFile, $variableTokenPointer));
	}

	public function testFindFirstTokenOnNextLineInDocComment(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleThree.php');
		$docBlockTokenPointer = TokenHelper::findNext($phpcsFile, T_DOC_COMMENT_OPEN_TAG, 0);
		self::assertTokenPointer(T_DOC_COMMENT_OPEN_TAG, 3, $phpcsFile, $docBlockTokenPointer);
		self::assertTokenPointer(
			T_DOC_COMMENT_WHITESPACE,
			4,
			$phpcsFile,
			TokenHelper::findFirstTokenOnNextLine($phpcsFile, $docBlockTokenPointer)
		);
	}

	public function testFindFirstTokenOnIndentedNextLine(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/forLoop.php');
		$tokens = $phpcsFile->getTokens();
		$forTokenPointer = TokenHelper::findNext($phpcsFile, T_FOR, 0);
		$nextLineTokenPointer = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $forTokenPointer);
		self::assertTokenPointer(T_WHITESPACE, 4, $phpcsFile, $nextLineTokenPointer);
		self::assertSame("\t", $tokens[$nextLineTokenPointer]['content']);
		$fooTokenPointer = TokenHelper::findNextAnyToken($phpcsFile, $nextLineTokenPointer + 1);
		self::assertTokenPointer(T_STRING, 4, $phpcsFile, $fooTokenPointer);
		self::assertSame('foo', $tokens[$fooTokenPointer]['content']);
	}

	public function testFindFirstTokenOnNonExistentNextLine(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleTwo.php');
		self::assertNull(TokenHelper::findFirstTokenOnNextLine($phpcsFile, TokenHelper::getLastTokenPointer($phpcsFile)));
	}

	public function testFindFirstTokenOnNonExistentNextLineAfterLastTokenInFile(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleTwo.php');
		self::assertNull(TokenHelper::findFirstTokenOnNextLine($phpcsFile, TokenHelper::getLastTokenPointer($phpcsFile) + 1));
	}

	public function testFindFirstNonWhitespaceOnNextLine(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleTwo.php');

		self::assertNull(TokenHelper::findFirstNonWhitespaceOnNextLine($phpcsFile, TokenHelper::getLastTokenPointer($phpcsFile) + 1));
	}

	public function testFindFirstNonWhitespaceOnPreviousLine(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleTwo.php');

		self::assertNull(TokenHelper::findFirstNonWhitespaceOnPreviousLine($phpcsFile, 0));
	}

	public function testGetContent(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleTwo.php');
		$variableTokenPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, 0);
		self::assertTokenPointer(T_VARIABLE, 3, $phpcsFile, $variableTokenPointer);
		$openParenthesisTokenPointer = TokenHelper::findNext($phpcsFile, T_OPEN_PARENTHESIS, 0);
		self::assertTokenPointer(T_OPEN_PARENTHESIS, 4, $phpcsFile, $openParenthesisTokenPointer);
		$content = TokenHelper::getContent($phpcsFile, $variableTokenPointer, $openParenthesisTokenPointer);
		self::assertSame(sprintf('$i++;%sfoo(', $phpcsFile->eolChar), $content);
	}

	public function testGetLastTokenPointer(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/sampleOne.php');
		$semicolonTokenPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, 0);
		self::assertTokenPointer(T_SEMICOLON, 3, $phpcsFile, $semicolonTokenPointer);
		$lastWhitespaceTokenPointer = TokenHelper::findNext($phpcsFile, T_WHITESPACE, $semicolonTokenPointer + 1);
		self::assertTokenPointer(T_WHITESPACE, 3, $phpcsFile, $lastWhitespaceTokenPointer);
		self::assertSame($lastWhitespaceTokenPointer, TokenHelper::getLastTokenPointer($phpcsFile));
	}

	public function testGetLastTokenPointerInEmptyFile(): void
	{
		try {
			$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/emptyFile.php');
			TokenHelper::getLastTokenPointer($phpcsFile);
			self::fail();
		} catch (EmptyFileException $e) {
			self::assertStringContainsString('emptyFile.php is empty', $e->getMessage());
			self::assertStringContainsString('emptyFile.php', $e->getFilename());
		}
	}

}
