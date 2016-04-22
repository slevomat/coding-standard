<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;

class TypeNameMatchesFileNameSniff implements \PHP_CodeSniffer_Sniff
{

	/** @var string[] path(string) => namespace */
	public $rootNamespaces = [];

	/** @var string[] path(string) => namespace */
	private $normalizedRootNamespaces;

	/** @var string[] */
	public $skipDirs = [];

	/** @var string[] */
	private $normalizedSkipDirs;

	/** @var string[] */
	public $ignoredNamespaces = [];

	/** @var string[] */
	private $normalizedIgnoredNamespaces;

	/** @var \SlevomatCodingStandard\Sniffs\Files\FilepathNamespaceExtractor */
	private $namespaceExtractor;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
		];
	}

	/**
	 * @return string[] path(string) => namespace
	 */
	private function getRootNamespaces(): array
	{
		if ($this->normalizedRootNamespaces === null) {
			$this->normalizedRootNamespaces = SniffSettingsHelper::normalizeAssociativeArray($this->rootNamespaces);
		}

		return $this->normalizedRootNamespaces;
	}

	/**
	 * @return string[]
	 */
	private function getSkipDirs(): array
	{
		if ($this->normalizedSkipDirs === null) {
			$this->normalizedSkipDirs = SniffSettingsHelper::normalizeArray($this->skipDirs);
		}

		return $this->normalizedSkipDirs;
	}

	/**
	 * @return string[]
	 */
	private function getIgnoredNamespaces(): array
	{
		if ($this->normalizedIgnoredNamespaces === null) {
			$this->normalizedIgnoredNamespaces = SniffSettingsHelper::normalizeArray($this->ignoredNamespaces);
		}

		return $this->normalizedIgnoredNamespaces;
	}

	private function getNamespaceExtractor(): FilepathNamespaceExtractor
	{
		if ($this->namespaceExtractor === null) {
			$this->namespaceExtractor = new FilepathNamespaceExtractor(
				$this->getRootNamespaces(),
				$this->getSkipDirs()
			);
		}

		return $this->namespaceExtractor;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$namePointer = $phpcsFile->findNext(T_STRING, $stackPointer + 1);
		$nameToken = $tokens[$namePointer];
		$typeName = $nameToken['content'];
		$namespacePointer = $phpcsFile->findPrevious(T_NAMESPACE, $stackPointer - 1);
		if ($namespacePointer !== false) {
			$namespaceName = '';
			while (true) {
				$namespaceNamePartPointer = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR, T_SEMICOLON], $namespacePointer + 1);
				if ($namespaceNamePartPointer === false) {
					break;
				}
				$namespaceNamePartToken = $tokens[$namespaceNamePartPointer];
				if ($namespaceNamePartToken['code'] === T_SEMICOLON) {
					break;
				}
				$namespaceName .= $namespaceNamePartToken['content'];
				$namespacePointer = $namespaceNamePartPointer;
			}

			$typeName = $namespaceName . '\\' . $typeName;
		} else {
			// skip types without a namespace
			return;
		}

		foreach ($this->getIgnoredNamespaces() as $ignoredNamespace) {
			if (StringHelper::startsWith($typeName, $ignoredNamespace . '\\')) {
				return;
			}
		}

		$expectedTypeName = $this->getNamespaceExtractor()->getTypeNameFromProjectPath(
			$phpcsFile->getFilename()
		);
		if ($typeName !== $expectedTypeName) {
			$phpcsFile->addError(
				sprintf(
					'%s name %s does not match filepath %s.',
					ucfirst($tokens[$stackPointer]['content']),
					$typeName,
					$phpcsFile->getFilename()
				),
				$namePointer
			);
		}
	}

}
