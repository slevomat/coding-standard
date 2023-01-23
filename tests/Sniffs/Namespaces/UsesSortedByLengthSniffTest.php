<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class UsesSortedByLengthSniffTest extends TestCase
{

	public function testIncorrectOrder(): void
	{
		self::assertSniffError(
			self::checkFile(__DIR__ . '/data/UsesSortedByLengthSniff/incorrectOrder.php'),
			4,
			UsesSortedByLengthSniff::CODE_INCORRECT_ORDER,
			'Second\FooObject'
		);
	}

	public function testCorrectOrderIgnoresUsesAfterClassesTraitsAndInterfaces(): void
	{
		self::assertNoSniffErrorInFile(
			self::checkFile(__DIR__ . '/data/UsesSortedByLengthSniff/correctOrder.php')
		);
	}

	public function testIncorrectOrderOfSimilarNamespaces(): void
	{
		self::assertSniffError(
			self::checkFile(__DIR__ . '/data/UsesSortedByLengthSniff/incorrectOrderSimilarNamespaces.php'),
			6,
			UsesSortedByLengthSniff::CODE_INCORRECT_ORDER,
			'FooBar'
		);
	}

	public function testFixable(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/UsesSortedByLengthSniff/fixableSortedByLengthUsesWithComments.php',
			[],
			[UsesSortedByLengthSniff::CODE_INCORRECT_ORDER]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableWithoutComments(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/UsesSortedByLengthSniff/fixableSortedByLengthUsesWithoutComments.php',
			[],
			[UsesSortedByLengthSniff::CODE_INCORRECT_ORDER]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableWithCommentBeforeFirst(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/UsesSortedByLengthSniff/fixableSortedByLengthUsesWithCommentBeforeFirst.php',
			[],
			[UsesSortedByLengthSniff::CODE_INCORRECT_ORDER]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableNotPsr12Compatible(): void
	{
		$report = self::checkFile(__DIR__ . '/data/UsesSortedByLengthSniff/fixableSortedByLengthUsesNotPsr12Compatible.php', [
			'psr12Compatible' => false,
		], [UsesSortedByLengthSniff::CODE_INCORRECT_ORDER]);
		self::assertAllFixedInFile($report);
	}

}
