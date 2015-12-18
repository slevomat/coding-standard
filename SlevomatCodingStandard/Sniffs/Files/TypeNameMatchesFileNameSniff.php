<?php

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Helpers\StringHelper;

class TypeNameMatchesFileNameSniff implements \PHP_CodeSniffer_Sniff
{

	/** @var string[] path(string) => namespace */
	public $rootNamespaces = [];

	/** @var string */
	public $skipDirs = [];

	/** @var string[] */
	public $ignoredNamespaces = [];

	/** @var \SlevomatCodingStandard\Sniffs\Files\FilepathNamespaceExtractor */
	private $namespaceExtractor;

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
		];
	}

	/**
	 * @return \SlevomatCodingStandard\Sniffs\Files\FilepathNamespaceExtractor
	 */
	private function getNamespaceExtractor()
	{
		if ($this->namespaceExtractor === null) {
			$this->namespaceExtractor = new FilepathNamespaceExtractor(
				$this->rootNamespaces,
				$this->skipDirs
			);
		}

		return $this->namespaceExtractor;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $stackPointer
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

		foreach ($this->ignoredNamespaces as $ignoredNamespace) {
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
