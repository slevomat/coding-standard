<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use function sprintf;
use const T_STRING;

class NamespaceHelperTest extends TestCase
{

	/**
	 * @return list<array{0: string}>
	 */
	public static function dataIsFullyQualifiedName(): array
	{
		return [
			['\Foo'],
			['\Foo\Bar'],
		];
	}

	/**
	 * @dataProvider dataIsFullyQualifiedName
	 */
	#[DataProvider('dataIsFullyQualifiedName')]
	public function testIsFullyQualifiedName(string $typeName): void
	{
		self::assertTrue(NamespaceHelper::isFullyQualifiedName($typeName));
	}

	/**
	 * @dataProvider dataIsFullyQualifiedName
	 */
	#[DataProvider('dataIsFullyQualifiedName')]
	public function testGetFullyQualifiedTypeNameUnchanged(string $typeName): void
	{
		self::assertSame($typeName, NamespaceHelper::getFullyQualifiedTypeName($typeName));
	}

	/**
	 * @return list<array{0: string}>
	 */
	public static function dataIsNotFullyQualifiedName(): array
	{
		return [
			['Bar'],
			['Foo\Bar'],
		];
	}

	/**
	 * @dataProvider dataIsNotFullyQualifiedName
	 */
	#[DataProvider('dataIsNotFullyQualifiedName')]
	public function testIsNotFullyQualifiedName(string $typeName): void
	{
		self::assertFalse(NamespaceHelper::isFullyQualifiedName($typeName));
	}

	/**
	 * @dataProvider dataIsNotFullyQualifiedName
	 */
	#[DataProvider('dataIsNotFullyQualifiedName')]
	public function testGetFullyQualifiedTypeNameChanged(string $typeName): void
	{
		self::assertSame(
			sprintf('\\%s', $typeName),
			NamespaceHelper::getFullyQualifiedTypeName($typeName),
		);
	}

	/**
	 * @return list<array{0: string}>
	 */
	public static function dataHasNamespace(): array
	{
		return [
			['\Foo\Bar'],
			['Foo\Bar'],
		];
	}

	/**
	 * @dataProvider dataHasNamespace
	 */
	#[DataProvider('dataHasNamespace')]
	public function testHasNamespace(string $typeName): void
	{
		self::assertTrue(NamespaceHelper::hasNamespace($typeName));
	}

	/**
	 * @return list<array{0: string}>
	 */
	public static function dataDoesNotHaveNamespace(): array
	{
		return [
			['Foo'],
			['\Foo'],
		];
	}

	/**
	 * @dataProvider dataDoesNotHaveNamespace
	 */
	#[DataProvider('dataDoesNotHaveNamespace')]
	public function testDoesNotHaveNamespace(string $typeName): void
	{
		self::assertFalse(NamespaceHelper::hasNamespace($typeName));
	}

	/**
	 * @return list<array{0: string, 1: list<string>}>
	 */
	public static function dataGetNameParts(): array
	{
		return [
			[
				'\Foo',
				['Foo'],
			],
			[
				'Foo',
				['Foo'],
			],
			[
				'\Foo\Bar\Baz',
				['Foo', 'Bar', 'Baz'],
			],
			[
				'Foo\Bar\Baz',
				['Foo', 'Bar', 'Baz'],
			],
		];
	}

	/**
	 * @dataProvider dataGetNameParts
	 * @param list<string> $parts
	 */
	#[DataProvider('dataGetNameParts')]
	public function testGetNameParts(string $namespace, array $parts): void
	{
		self::assertSame($parts, NamespaceHelper::getNameParts($namespace));
	}

	public function testFindCurrentNamespaceName(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/namespacedFile.php');
		$namespace = NamespaceHelper::findCurrentNamespaceName(
			$phpcsFile,
			TokenHelper::getLastTokenPointer($phpcsFile),
		);
		self::assertSame('Foo\Bar', $namespace);
	}

	public function testFindCurrentNamespaceNameInFileWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/fileWithoutNamespace.php');
		self::assertNull(
			NamespaceHelper::findCurrentNamespaceName(
				$phpcsFile,
				TokenHelper::getLastTokenPointer($phpcsFile),
			),
		);
	}

	public function testClosestNamespaceNameWithMultipleNamespacesInFile(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/multipleNamespaces.php');
		$namespace = NamespaceHelper::findCurrentNamespaceName(
			$phpcsFile,
			TokenHelper::getLastTokenPointer($phpcsFile),
		);
		self::assertSame('Lorem\Ipsum', $namespace);
	}

	public function testGetAllNamespacesWithMultipleNamespacesInFile(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/multipleNamespaces.php');
		$namespaces = NamespaceHelper::getAllNamespacesPointers($phpcsFile);
		self::assertEquals([2, 16], $namespaces);
	}

	public function testResolveClassNameWithMoreNamespaces(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/moreNamespaces.php');

		self::assertSame(
			'\Something\Foo',
			NamespaceHelper::resolveClassName($phpcsFile, 'Foo', $this->findPointerByLineAndType($phpcsFile, 7, T_STRING)),
		);
		self::assertSame(
			'\Anything\Foo',
			NamespaceHelper::resolveClassName($phpcsFile, 'Foo', $this->findPointerByLineAndType($phpcsFile, 16, T_STRING)),
		);
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataGetUnqualifiedNameFromFullyQualifiedName(): array
	{
		return [
			[
				'Foo',
				'\Foo',
			],
			[
				'Foo',
				'Foo',
			],
			[
				'Baz',
				'\Foo\Bar\Baz',
			],
			[
				'Baz',
				'Foo\Bar\Baz',
			],
		];
	}

	/**
	 * @dataProvider dataGetUnqualifiedNameFromFullyQualifiedName
	 */
	#[DataProvider('dataGetUnqualifiedNameFromFullyQualifiedName')]
	public function testGetUnqualifiedNameFromFullyQualifiedName(string $unqualifiedName, string $fullyQualifiedName): void
	{
		self::assertSame($unqualifiedName, NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($fullyQualifiedName));
	}

	/**
	 * @return list<array{0: string}>
	 */
	public static function dataIsQualifiedName(): array
	{
		return [
			['\Foo'],
			['\Foo\Bar'],
			['Foo\Bar'],
		];
	}

	/**
	 * @dataProvider dataIsQualifiedName
	 */
	#[DataProvider('dataIsQualifiedName')]
	public function testIsQualifiedName(string $name): void
	{
		self::assertTrue(NamespaceHelper::isQualifiedName($name));
	}

	/**
	 * @return list<array{0: string}>
	 */
	public static function dataIsNotQualifiedName(): array
	{
		return [
			['Foo'],
		];
	}

	/**
	 * @dataProvider dataIsNotQualifiedName
	 */
	#[DataProvider('dataIsNotQualifiedName')]
	public function testIsNotQualifiedName(string $name): void
	{
		self::assertFalse(NamespaceHelper::isQualifiedName($name));
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataNormalizeToCanonicalName(): array
	{
		return [
			[
				'Foo',
				'Foo',
			],
			[
				'Foo',
				'\Foo',
			],
			[
				'Foo\Bar\Baz',
				'Foo\Bar\Baz',
			],
			[
				'Foo\Bar\Baz',
				'\Foo\Bar\Baz',
			],
		];
	}

	/**
	 * @dataProvider dataNormalizeToCanonicalName
	 */
	#[DataProvider('dataNormalizeToCanonicalName')]
	public function testNormalizeToCanonicalName(string $normalizedName, string $originalName): void
	{
		self::assertSame($normalizedName, NamespaceHelper::normalizeToCanonicalName($originalName));
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataTypeIsInNamespace(): array
	{
		return [
			[
				'Foo\Bar',
				'Foo',
			],
			[
				'\Foo\Bar',
				'Foo',
			],
			[
				'Lorem\Ipsum\Dolor',
				'Lorem\Ipsum',
			],
			[
				'\Lorem\Ipsum\Dolor',
				'Lorem\Ipsum',
			],
		];
	}

	/**
	 * @dataProvider dataTypeIsInNamespace
	 */
	#[DataProvider('dataTypeIsInNamespace')]
	public function testTypeIsInNamespace(string $typeName, string $namespace): void
	{
		self::assertTrue(NamespaceHelper::isTypeInNamespace($typeName, $namespace));
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataTypeIsNotInNamespace(): array
	{
		return [
			[
				'Foo\Bar',
				'Bar',
			],
			[
				'\Foo\Bar',
				'Bar',
			],
			[
				'Fooo\Bar',
				'Foo',
			],
			[
				'\Fooo\Bar',
				'Foo',
			],
			[
				'Lorem\Ipsum\DolorBar',
				'Lorem\Ipsum\Dolor',
			],
			[
				'\Lorem\Ipsum\DolorBar',
				'Lorem\Ipsum\Dolor',
			],
		];
	}

	/**
	 * @dataProvider dataTypeIsNotInNamespace
	 */
	#[DataProvider('dataTypeIsNotInNamespace')]
	public function testTypeIsNotInNamespace(string $typeName, string $namespace): void
	{
		self::assertFalse(NamespaceHelper::isTypeInNamespace($typeName, $namespace));
	}

}
