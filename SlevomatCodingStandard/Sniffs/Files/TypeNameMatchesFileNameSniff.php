<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;

class TypeNameMatchesFileNameSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_NO_MATCH_BETWEEN_TYPE_NAME_AND_FILE_NAME = 'NoMatchBetweenTypeNameAndFileName';

	/** @var string[] path(string) => namespace */
	public $rootNamespaces = [];

	/** @var string[] index(integer) => extension */
	public $extensions = ['php'];

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
				$this->getSkipDirs(),
				$this->extensions
			);
		}

		return $this->namespaceExtractor;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $typePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $typePointer)
	{
		$tokens = $phpcsFile->getTokens();
		$namePointer = $phpcsFile->findNext(T_STRING, $typePointer + 1);

		$namespacePointer = $phpcsFile->findPrevious(T_NAMESPACE, $typePointer - 1);
		if ($namespacePointer === false) {
			// Skip types without a namespace
			return;
		}

		$typeName = NamespaceHelper::normalizeToCanonicalName(ClassHelper::getFullyQualifiedName($phpcsFile, $typePointer));

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
					ucfirst($tokens[$typePointer]['content']),
					$typeName,
					$phpcsFile->getFilename()
				),
				$namePointer,
				self::CODE_NO_MATCH_BETWEEN_TYPE_NAME_AND_FILE_NAME
			);
		}
	}

}
