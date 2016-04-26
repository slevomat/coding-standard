<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class UnusedPrivateElementsSniff implements \PHP_CodeSniffer_Sniff
{

	const NAME = 'SlevomatCodingStandard.Classes.UnusedPrivateElements';

	const CODE_UNUSED_PROPERTY = 'unusedProperty';

	const CODE_WRITE_ONLY_PROPERTY = 'writeOnlyProperty';

	const CODE_UNUSED_METHOD = 'unusedMethod';

	/** @var string[] */
	public $alwaysUsedPropertiesAnnotations = [];

	/** @var string[] */
	private $normalizedAlwaysUsedPropertiesAnnotations;

	/** @var string[] */
	public $alwaysUsedPropertiesSuffixes = [];

	/** @var string[] */
	private $normalizedAlwaysUsedPropertiesSuffixes;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_CLASS,
		];
	}

	/**
	 * @return string[]
	 */
	private function getAlwaysUsedPropertiesAnnotations(): array
	{
		if ($this->normalizedAlwaysUsedPropertiesAnnotations === null) {
			$this->normalizedAlwaysUsedPropertiesAnnotations = SniffSettingsHelper::normalizeArray($this->alwaysUsedPropertiesAnnotations);
		}

		return $this->normalizedAlwaysUsedPropertiesAnnotations;
	}

	/**
	 * @return string[]
	 */
	private function getAlwaysUsedPropertiesSuffixes(): array
	{
		if ($this->normalizedAlwaysUsedPropertiesSuffixes === null) {
			$this->normalizedAlwaysUsedPropertiesSuffixes = SniffSettingsHelper::normalizeArray($this->alwaysUsedPropertiesSuffixes);
		}

		return $this->normalizedAlwaysUsedPropertiesSuffixes;
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $classPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $classPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$classToken = $tokens[$classPointer];
		$reportedProperties = $this->getProperties($phpcsFile, $tokens, $classToken);
		$reportedMethods = $this->getMethods($phpcsFile, $tokens, $classToken);

		if (count($reportedProperties) + count($reportedMethods) === 0) {
			return;
		}

		$writeOnlyProperties = [];
		$findUsagesStartTokenPointer = $classToken['scope_opener'] + 1;

		while (($propertyAccessTokenPointer = $phpcsFile->findNext([T_VARIABLE, T_SELF, T_STATIC], $findUsagesStartTokenPointer, $classToken['scope_closer'])) !== false) {
			$propertyAccessToken = $tokens[$propertyAccessTokenPointer];
			if ($propertyAccessToken['content'] === '$this') {
				$objectOperatorTokenPointer = TokenHelper::findNextEffective($phpcsFile, $propertyAccessTokenPointer + 1);
				$objectOperatorToken = $tokens[$objectOperatorTokenPointer];
				if ($objectOperatorToken['code'] !== T_OBJECT_OPERATOR) {
					// $this not followed by ->
					$findUsagesStartTokenPointer = $propertyAccessTokenPointer + 1;
					continue;
				}

				$propertyNameTokenPointer = TokenHelper::findNextEffective($phpcsFile, $objectOperatorTokenPointer + 1);
				$propertyNameToken = $tokens[$propertyNameTokenPointer];
				$name = $propertyNameToken['content'];
				if ($propertyNameToken['code'] !== T_STRING) {
					// $this-> but not accessing a specific property (e. g. $this->$foo or $this->{$foo})
					$findUsagesStartTokenPointer = $propertyNameTokenPointer + 1;
					continue;
				}
				$methodCallTokenPointer = TokenHelper::findNextEffective($phpcsFile, $propertyNameTokenPointer + 1);
				$methodCallToken = $tokens[$methodCallTokenPointer];
				if ($methodCallToken['code'] === T_OPEN_PARENTHESIS) {
					// calling a method on $this
					$findUsagesStartTokenPointer = $methodCallTokenPointer + 1;
					unset($reportedMethods[$name]);
					continue;
				}

				$assignTokenPointer = TokenHelper::findNextEffective($phpcsFile, $propertyNameTokenPointer + 1);
				$assignToken = $tokens[$assignTokenPointer];
				if ($assignToken['code'] === T_EQUAL) {
					// assigning value to a property - note possible write-only property
					$findUsagesStartTokenPointer = $assignTokenPointer + 1;
					$writeOnlyProperties[$name] = $propertyNameTokenPointer;
					continue;
				}

				if (isset($reportedProperties[$name])) {
					unset($reportedProperties[$name]);
				}

				$findUsagesStartTokenPointer = $propertyNameTokenPointer + 1;
			} elseif (in_array($propertyAccessToken['code'], [T_SELF, T_STATIC], true)) {
				$doubleColonTokenPointer = TokenHelper::findNextEffective($phpcsFile, $propertyAccessTokenPointer + 1);
				$doubleColonToken = $tokens[$doubleColonTokenPointer];
				if ($doubleColonToken['code'] !== T_DOUBLE_COLON) {
					// self or static not followed by ::
					$findUsagesStartTokenPointer = $doubleColonTokenPointer + 1;
					continue;
				}

				$methodNameTokenPointer = TokenHelper::findNextEffective($phpcsFile, $doubleColonTokenPointer + 1);
				$methodNameToken = $tokens[$methodNameTokenPointer];
				if ($methodNameToken['code'] !== T_STRING) {
					// self:: or static:: not followed by a string - possible static property access
					$findUsagesStartTokenPointer = $methodNameTokenPointer + 1;
					continue;
				}

				$methodCallTokenPointer = TokenHelper::findNextEffective($phpcsFile, $methodNameTokenPointer + 1);
				$methodCallToken = $tokens[$methodCallTokenPointer];
				if ($methodCallToken['code'] !== T_OPEN_PARENTHESIS) {
					// self::string or static::string not followed by ( - possible constant access
					$findUsagesStartTokenPointer = $methodCallTokenPointer + 1;
					continue;
				}

				$name = $methodNameToken['content'];
				if (isset($reportedMethods[$name])) {
					unset($reportedMethods[$name]);
				}

				$findUsagesStartTokenPointer = $methodCallTokenPointer + 1;
			} else {
				$findUsagesStartTokenPointer = $propertyAccessTokenPointer + 1;
			}

			if (count($reportedProperties) + count($reportedMethods) === 0) {
				return;
			}
		}

		if (count($reportedProperties) + count($reportedMethods) === 0) {
			return;
		}

		$classNamePointer = $phpcsFile->findNext(T_STRING, $classPointer);
		$className = $tokens[$classNamePointer]['content'];

		foreach ($reportedProperties as $name => $propertyTokenPointer) {
			if (isset($writeOnlyProperties[$name])) {
				if (!SuppressHelper::isSniffSuppressed($phpcsFile, $propertyTokenPointer, $this->getSniffName(self::CODE_WRITE_ONLY_PROPERTY))) {
					$phpcsFile->addError(sprintf(
						'Class %s contains write-only property: $%s',
						$className,
						$name
					), $propertyTokenPointer, self::CODE_WRITE_ONLY_PROPERTY);
				}
			} else {
				if (!SuppressHelper::isSniffSuppressed($phpcsFile, $propertyTokenPointer, $this->getSniffName(self::CODE_UNUSED_PROPERTY))) {
					$phpcsFile->addError(sprintf(
						'Class %s contains unused property: $%s',
						$className,
						$name
					), $propertyTokenPointer, self::CODE_UNUSED_PROPERTY);
				}
			}
		}

		foreach ($reportedMethods as $name => $methodTokenPointer) {
			if (!SuppressHelper::isSniffSuppressed($phpcsFile, $methodTokenPointer, $this->getSniffName(self::CODE_UNUSED_METHOD))) {
				$phpcsFile->addError(sprintf(
					'Class %s contains unused private method: %s',
					$className,
					$name
				), $methodTokenPointer, self::CODE_UNUSED_METHOD);
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param mixed[] $tokens
	 * @param mixed[] $classToken
	 * @return int[] string(name) => pointer
	 */
	private function getProperties(\PHP_CodeSniffer_File $phpcsFile, array $tokens, array $classToken): array
	{
		$reportedProperties = [];
		$findPropertiesStartTokenPointer = $classToken['scope_opener'] + 1;
		while (($propertyTokenPointer = $phpcsFile->findNext(T_VARIABLE, $findPropertiesStartTokenPointer, $classToken['scope_closer'])) !== false) {
			$visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $propertyTokenPointer - 1);
			$visibilityModifiedToken = $tokens[$visibilityModifiedTokenPointer];
			if ($visibilityModifiedToken['code'] !== T_PRIVATE) {
				$findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
				continue;
			}

			$findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
			$phpDocTags = $this->getPhpDocTags($phpcsFile, $tokens, $visibilityModifiedTokenPointer);
			foreach ($phpDocTags as $tag) {
				preg_match('#([@a-zA-Z\\\]+)#', $tag, $matches);
				if (in_array($matches[1], $this->getAlwaysUsedPropertiesAnnotations(), true)) {
					continue 2;
				}
			}

			$propertyToken = $tokens[$propertyTokenPointer];
			$name = substr($propertyToken['content'], 1);

			foreach ($this->getAlwaysUsedPropertiesSuffixes() as $prefix) {
				if (StringHelper::endsWith($name, $prefix)) {
					continue 2;
				}
			}

			$reportedProperties[$name] = $propertyTokenPointer;
		}

		return $reportedProperties;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param mixed[] $tokens
	 * @param int $privateTokenPointer
	 * @return string[]
	 */
	private function getPhpDocTags(\PHP_CodeSniffer_File $phpcsFile, array $tokens, int $privateTokenPointer): array
	{
		$phpDocTokenCloseTagPointer = TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $privateTokenPointer - 1);
		$phpDocTokenCloseTag = $tokens[$phpDocTokenCloseTagPointer];
		if ($phpDocTokenCloseTag['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
			return [];
		}

		$tags = [];
		$findPhpDocTagPointer = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $phpDocTokenCloseTagPointer - 1) + 1;
		while (($phpDocTagTokenPointer = $phpcsFile->findNext([T_DOC_COMMENT_TAG], $findPhpDocTagPointer, $phpDocTokenCloseTagPointer)) !== false) {
			$phpDocTagToken = $tokens[$phpDocTagTokenPointer];
			$tags[] = $phpDocTagToken['content'];

			$findPhpDocTagPointer++;
		}

		return $tags;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param mixed[] $tokens
	 * @param mixed[] $classToken
	 * @return int[] string(name) => pointer
	 */
	private function getMethods(\PHP_CodeSniffer_File $phpcsFile, array $tokens, array $classToken): array
	{
		$reportedMethods = [];
		$findMethodsStartTokenPointer = $classToken['scope_opener'] + 1;
		while (($methodTokenPointer = $phpcsFile->findNext(T_FUNCTION, $findMethodsStartTokenPointer, $classToken['scope_closer'])) !== false) {
			$visibilityModifier = $this->findVisibilityModifier($phpcsFile, $tokens, $methodTokenPointer);
			if ($visibilityModifier === null || $visibilityModifier !== T_PRIVATE) {
				$findMethodsStartTokenPointer = $methodTokenPointer + 1;
				continue;
			}

			$namePointer = TokenHelper::findNextEffective($phpcsFile, $methodTokenPointer + 1);
			if ($namePointer === null) {
				$findMethodsStartTokenPointer = $methodTokenPointer + 1;
				continue;
			}

			$methodName = $tokens[$namePointer]['content'];

			if ($methodName !== '__construct') {
				$reportedMethods[$methodName] = $methodTokenPointer;
			}
			$findMethodsStartTokenPointer = $methodTokenPointer + 1;
		}

		return $reportedMethods;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param mixed[] $tokens
	 * @param int $methodTokenPointer
	 * @return int|null
	 */
	private function findVisibilityModifier(\PHP_CodeSniffer_File $phpcsFile, array $tokens, int $methodTokenPointer)
	{
		$visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $methodTokenPointer - 1);
		$visibilityModifiedToken = $tokens[$visibilityModifiedTokenPointer];
		if (in_array($visibilityModifiedToken['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
			return $visibilityModifiedToken['code'];
		} elseif (in_array($visibilityModifiedToken['code'], [T_ABSTRACT, T_STATIC], true)) {
			return $this->findVisibilityModifier($phpcsFile, $tokens, $visibilityModifiedTokenPointer);
		}

		return null;
	}

}
