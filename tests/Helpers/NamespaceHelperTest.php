<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function sprintf;
use const T_STRING;

class NamespaceHelperTest extends TestCase
{

	/**
	 * @return string[][]
	 */
	public function dataIsFullyQualifiedName(): array
	{
		return [
			[
				'\Foo',
				'\Foo\Bar',
			],
		];
	}

	/**
	 * @dataProvider dataIsFullyQualifiedName
	 * @param string $typeName
	 */
	public function testIsFullyQualifiedName(string $typeName): void
	{
		self::assertTrue(NamespaceHelper::isFullyQualifiedName($typeName));
	}

	/**
	 * @dataProvider dataIsFullyQualifiedName
	 * @param string $typeName
	 */
	public function testGetFullyQualifiedTypeNameUnchanged(string $typeName): void
	{
		self::assertSame($typeName, NamespaceHelper::getFullyQualifiedTypeName($typeName));
	}

	/**
	 * @return string[][]
	 */
	public function dataIsNotFullyQualifiedName(): array
	{
		return [
			[
				'Bar',
				'Foo\Bar',
			],
		];
	}

	/**
	 * @dataProvider dataIsNotFullyQualifiedName
	 * @param string $typeName
	 */
	public function testIsNotFullyQualifiedName(string $typeName): void
	{
		self::assertFalse(NamespaceHelper::isFullyQualifiedName($typeName));
	}

	/**
	 * @dataProvider dataIsNotFullyQualifiedName
	 * @param string $typeName
	 */
	public function testGetFullyQualifiedTypeNameChanged(string $typeName): void
	{
		self::assertSame(
			sprintf('\\%s', $typeName),
			NamespaceHelper::getFullyQualifiedTypeName($typeName)
		);
	}

	/**
	 * @return string[][]
	 */
	public function dataHasNamespace(): array
	{
		return [
			[
				'\Foo\Bar',
				'Foo\Bar',
			],
		];
	}

	/**
	 * @dataProvider dataHasNamespace
	 * @param string $typeName
	 */
	public function testHasNamespace(string $typeName): void
	{
		self::assertTrue(NamespaceHelper::hasNamespace($typeName));
	}

	/**
	 * @return string[][]
	 */
	public function dataDoesNotHaveNamespace(): array
	{
		return [
			[
				'Foo',
				'\Foo',
			],
		];
	}

	/**
	 * @dataProvider dataDoesNotHaveNamespace
	 * @param string $typeName
	 */
	public function testDoesNotHaveNamespace(string $typeName): void
	{
		self::assertFalse(NamespaceHelper::hasNamespace($typeName));
	}

	/**
	 * @return mixed[][]
	 */
	public function dataGetNameParts(): array
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
	 * @param string $namespace
	 * @param string[] $parts
	 */
	public function testGetNameParts(string $namespace, array $parts): void
	{
		self::assertSame($parts, NamespaceHelper::getNameParts($namespace));
	}

	public function testFindCurrentNamespaceName(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/namespacedFile.php');
		$namespace = NamespaceHelper::findCurrentNamespaceName(
			$phpcsFile,
			TokenHelper::getLastTokenPointer($phpcsFile)
		);
		self::assertSame('Foo\Bar', $namespace);
	}

	public function testFindCurrentNamespaceNameInFileWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/fileWithoutNamespace.php');
		self::assertNull(
			NamespaceHelper::findCurrentNamespaceName(
				$phpcsFile,
				TokenHelper::getLastTokenPointer($phpcsFile)
			)
		);
	}

	public function testClosestNamespaceNameWithMultipleNamespacesInFile(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/multipleNamespaces.php');
		$namespace = NamespaceHelper::findCurrentNamespaceName(
			$phpcsFile,
			TokenHelper::getLastTokenPointer($phpcsFile)
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
			NamespaceHelper::resolveClassName($phpcsFile, 'Foo', $this->findPointerByLineAndType($phpcsFile, 7, T_STRING))
		);
		self::assertSame(
			'\Anything\Foo',
			NamespaceHelper::resolveClassName($phpcsFile, 'Foo', $this->findPointerByLineAndType($phpcsFile, 16, T_STRING))
		);
	}

	/**
	 * @return string[][]
	 */
	public function dataGetUnqualifiedNameFromFullyQualifiedName(): array
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
	 * @param string $unqualifiedName
	 * @param string $fullyQualifiedName
	 */
	public function testGetUnqualifiedNameFromFullyQualifiedName(string $unqualifiedName, string $fullyQualifiedName): void
	{
		self::assertSame($unqualifiedName, NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($fullyQualifiedName));
	}

	/**
	 * @return string[][]
	 */
	public function dataIsQualifiedName(): array
	{
		return [
			['\Foo'],
			['\Foo\Bar'],
			['Foo\Bar'],
		];
	}

	/**
	 * @dataProvider dataIsQualifiedName
	 * @param string $name
	 */
	public function testIsQualifiedName(string $name): void
	{
		self::assertTrue(NamespaceHelper::isQualifiedName($name));
	}

	/**
	 * @return string[][]
	 */
	public function dataIsNotQualifiedName(): array
	{
		return [
			['Foo'],
		];
	}

	/**
	 * @dataProvider dataIsNotQualifiedName
	 * @param string $name
	 */
	public function testIsNotQualifiedName(string $name): void
	{
		self::assertFalse(NamespaceHelper::isQualifiedName($name));
	}

	/**
	 * @return string[][]
	 */
	public function dataNormalizeToCanonicalName(): array
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
	 * @param string $normalizedName
	 * @param string $originalName
	 */
	public function testNormalizeToCanonicalName(string $normalizedName, string $originalName): void
	{
		self::assertSame($normalizedName, NamespaceHelper::normalizeToCanonicalName($originalName));
	}

	/**
	 * @return string[][]
	 */
	public function dataTypeIsInNamespace(): array
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
	 * @param string $typeName
	 * @param string $namespace
	 */
	public function testTypeIsInNamespace(string $typeName, string $namespace): void
	{
		self::assertTrue(NamespaceHelper::isTypeInNamespace($typeName, $namespace));
	}

	/**
	 * @return string[][]
	 */
	public function dataTypeIsNotInNamespace(): array
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
	 * @param string $typeName
	 * @param string $namespace
	 */
	public function testTypeIsNotInNamespace(string $typeName, string $namespace): void
	{
		self::assertFalse(NamespaceHelper::isTypeInNamespace($typeName, $namespace));
	}

}
