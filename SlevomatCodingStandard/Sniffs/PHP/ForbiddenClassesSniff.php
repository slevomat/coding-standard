<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_key_exists;
use function array_pop;
use function count;
use function in_array;
use function is_array;
use function sprintf;
use function strlen;
use function strtolower;
use const T_DOUBLE_COLON;
use const T_EXTENDS;
use const T_IMPLEMENTS;
use const T_NEW;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_USE;

class ForbiddenClassesSniff implements Sniff
{

	public const CODE_FORBIDDEN_CLASS = 'ForbiddenClass';
	public const CODE_FORBIDDEN_PARENT_CLASS = 'ForbiddenParentClass';
	public const CODE_FORBIDDEN_INTERFACE = 'ForbiddenInterface';
	public const CODE_FORBIDDEN_TRAIT = 'ForbiddenTrait';

	/** @var array<string, (string|null)> */
	public array $forbiddenClasses = [];

	/** @var array<string, (string|null)> */
	public array $forbiddenExtends = [];

	/** @var array<string, (string|null)> */
	public array $forbiddenInterfaces = [];

	/** @var array<string, (string|null)> */
	public array $forbiddenTraits = [];

	/** @var list<string> */
	private static array $keywordReferences = ['self', 'parent', 'static'];

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		$searchTokens = [];

		if (count($this->forbiddenClasses) > 0) {
			$this->forbiddenClasses = self::normalizeInputOption($this->forbiddenClasses);
			$searchTokens[] = T_NEW;
			$searchTokens[] = T_DOUBLE_COLON;
		}

		if (count($this->forbiddenExtends) > 0) {
			$this->forbiddenExtends = self::normalizeInputOption($this->forbiddenExtends);
			$searchTokens[] = T_EXTENDS;
		}

		if (count($this->forbiddenInterfaces) > 0) {
			$this->forbiddenInterfaces = self::normalizeInputOption($this->forbiddenInterfaces);
			$searchTokens[] = T_IMPLEMENTS;
		}

		if (count($this->forbiddenTraits) > 0) {
			$this->forbiddenTraits = self::normalizeInputOption($this->forbiddenTraits);
			$searchTokens[] = T_USE;
		}

		return $searchTokens;
	}

	public function process(File $phpcsFile, int $tokenPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$tokenPointer];
		$nameTokens = [...TokenHelper::NAME_TOKEN_CODES, ...TokenHelper::INEFFECTIVE_TOKEN_CODES];

		if (
			$token['code'] === T_IMPLEMENTS
			|| (
				$token['code'] === T_USE
				&& UseStatementHelper::isTraitUse($phpcsFile, $tokenPointer)
			)
		) {
			$endTokenPointer = TokenHelper::findNext(
				$phpcsFile,
				[T_SEMICOLON, T_OPEN_CURLY_BRACKET],
				$tokenPointer,
			);
			$references = $this->getAllReferences($phpcsFile, $tokenPointer, $endTokenPointer);

			if ($token['code'] === T_IMPLEMENTS) {
				$this->checkReferences($phpcsFile, $tokenPointer, $references, $this->forbiddenInterfaces);
			} else {
				// Fixer does not work when traits contains aliases
				$this->checkReferences(
					$phpcsFile,
					$tokenPointer,
					$references,
					$this->forbiddenTraits,
					$tokens[$endTokenPointer]['code'] !== T_OPEN_CURLY_BRACKET,
				);
			}
		} elseif (in_array($token['code'], [T_NEW, T_EXTENDS], true)) {
			$endTokenPointer = TokenHelper::findNextExcluding($phpcsFile, $nameTokens, $tokenPointer + 1);
			$references = $this->getAllReferences($phpcsFile, $tokenPointer, $endTokenPointer);

			$this->checkReferences(
				$phpcsFile,
				$tokenPointer,
				$references,
				$token['code'] === T_NEW ? $this->forbiddenClasses : $this->forbiddenExtends,
			);
		} elseif ($token['code'] === T_DOUBLE_COLON && !$this->isTraitsConflictResolutionToken($token)) {
			$startTokenPointer = TokenHelper::findPreviousExcluding($phpcsFile, $nameTokens, $tokenPointer - 1);
			$references = $this->getAllReferences($phpcsFile, $startTokenPointer, $tokenPointer);

			$this->checkReferences($phpcsFile, $tokenPointer, $references, $this->forbiddenClasses);
		}
	}

	/**
	 * @param list<array{fullyQualifiedName: string, pointer: int}> $references
	 * @param array<string, (string|null)> $forbiddenNames
	 */
	private function checkReferences(
		File $phpcsFile,
		int $tokenPointer,
		array $references,
		array $forbiddenNames,
		bool $isFixable = true
	): void
	{
		$token = $phpcsFile->getTokens()[$tokenPointer];
		$details = [
			T_NEW => ['class', self::CODE_FORBIDDEN_CLASS],
			T_DOUBLE_COLON => ['class', self::CODE_FORBIDDEN_CLASS],
			T_EXTENDS => ['as a parent class', self::CODE_FORBIDDEN_PARENT_CLASS],
			T_IMPLEMENTS => ['interface', self::CODE_FORBIDDEN_INTERFACE],
			T_USE => ['trait', self::CODE_FORBIDDEN_TRAIT],
		];

		foreach ($references as $reference) {
			if (!array_key_exists($reference['fullyQualifiedName'], $forbiddenNames)) {
				continue;
			}

			$alternative = $forbiddenNames[$reference['fullyQualifiedName']];
			[$nameType, $code] = $details[$token['code']];

			if ($alternative === null) {
				$phpcsFile->addError(
					sprintf('Usage of %s %s is forbidden.', $reference['fullyQualifiedName'], $nameType),
					$reference['pointer'],
					$code,
				);
			} elseif (!$isFixable) {
				$phpcsFile->addError(
					sprintf(
						'Usage of %s %s is forbidden, use %s instead.',
						$reference['fullyQualifiedName'],
						$nameType,
						$alternative,
					),
					$reference['pointer'],
					$code,
				);
			} else {
				$fix = $phpcsFile->addFixableError(
					sprintf(
						'Usage of %s %s is forbidden, use %s instead.',
						$reference['fullyQualifiedName'],
						$nameType,
						$alternative,
					),
					$reference['pointer'],
					$code,
				);
				if (!$fix) {
					continue;
				}

				$phpcsFile->fixer->beginChangeset();

				FixerHelper::change($phpcsFile, $reference['pointer'], $reference['pointer'], $alternative);

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * @param array<string, array<int, int|string>|int|string> $token
	 */
	private function isTraitsConflictResolutionToken(array $token): bool
	{
		return is_array($token['conditions']) && array_pop($token['conditions']) === T_USE;
	}

	/**
	 * @return list<array{fullyQualifiedName: string, pointer: int}>
	 */
	private function getAllReferences(File $phpcsFile, int $startPointer, int $endPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		// Always ignore first token
		$startPointer++;
		$references = [];

		while ($startPointer < $endPointer) {
			$referencePointer = TokenHelper::findNext(
				$phpcsFile,
				[...TokenHelper::CLASS_KEYWORD_CODES, ...TokenHelper::NAME_TOKEN_CODES],
				$startPointer,
			);

			if ($referencePointer === null) {
				break;
			}

			if (in_array($tokens[$referencePointer]['code'], TokenHelper::CLASS_KEYWORD_CODES, true)) {
				$startPointer = $referencePointer + 1;
				continue;
			}

			$reference = $tokens[$referencePointer]['content'];

			if (
				strlen($reference) !== 0
				&& !in_array(strtolower($reference), self::$keywordReferences, true)
			) {
				$references[] = [
					'fullyQualifiedName' => NamespaceHelper::resolveClassName($phpcsFile, $reference, $startPointer),
					'pointer' => $referencePointer,
				];
			}

			$startPointer = $referencePointer + 1;
		}

		return $references;
	}

	/**
	 * @param array<string, (string|null)> $option
	 * @return array<string, (string|null)>
	 */
	private static function normalizeInputOption(array $option): array
	{
		$forbiddenClasses = [];
		foreach ($option as $forbiddenClass => $alternative) {
			$forbiddenClasses[self::normalizeClassName($forbiddenClass)] = self::normalizeClassName($alternative);
		}

		return $forbiddenClasses;
	}

	private static function normalizeClassName(?string $typeName): ?string
	{
		if ($typeName === null || strlen($typeName) === 0 || strtolower($typeName) === 'null') {
			return null;
		}

		return NamespaceHelper::getFullyQualifiedTypeName($typeName);
	}

}
