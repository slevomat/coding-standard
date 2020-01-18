<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_flip;
use function array_key_exists;
use function array_merge;
use function array_values;
use function assert;
use function count;
use function in_array;
use function preg_replace;
use function preg_split;
use function sprintf;
use function str_repeat;
use function strtolower;
use const T_CLOSE_CURLY_BRACKET;
use const T_CONST;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_SEMICOLON;
use const T_STATIC;
use const T_USE;
use const T_VARIABLE;
use const T_WHITESPACE;

class ClassStructureSniff implements Sniff
{

	public const CODE_INCORRECT_GROUP_ORDER = 'IncorrectGroupOrder';

	private const GROUP_USES = 'uses';
	private const GROUP_PUBLIC_CONSTANTS = 'public constants';
	private const GROUP_PROTECTED_CONSTANTS = 'protected constants';
	private const GROUP_PRIVATE_CONSTANTS = 'private constants';
	private const GROUP_PUBLIC_PROPERTIES = 'public properties';
	private const GROUP_PUBLIC_STATIC_PROPERTIES = 'public static properties';
	private const GROUP_PROTECTED_PROPERTIES = 'protected properties';
	private const GROUP_PROTECTED_STATIC_PROPERTIES = 'protected static properties';
	private const GROUP_PRIVATE_PROPERTIES = 'private properties';
	private const GROUP_PRIVATE_STATIC_PROPERTIES = 'private static properties';
	private const GROUP_CONSTRUCTOR = 'constructor';
	private const GROUP_STATIC_CONSTRUCTORS = 'static constructors';
	private const GROUP_DESTRUCTOR = 'destructor';
	private const GROUP_MAGIC_METHODS = 'magic methods';
	private const GROUP_PUBLIC_METHODS = 'public methods';
	private const GROUP_PUBLIC_STATIC_METHODS = 'public static methods';
	private const GROUP_PROTECTED_METHODS = 'protected methods';
	private const GROUP_PROTECTED_STATIC_METHODS = 'protected static methods';
	private const GROUP_PRIVATE_METHODS = 'private methods';
	private const GROUP_PRIVATE_STATIC_METHODS = 'private static methods';

	private const SPECIAL_METHODS = [
		'__construct' => self::GROUP_CONSTRUCTOR,
		'__destruct' => self::GROUP_DESTRUCTOR,
		'__call' => self::GROUP_MAGIC_METHODS,
		'__callStatic' => self::GROUP_MAGIC_METHODS,
		'__get' => self::GROUP_MAGIC_METHODS,
		'__set' => self::GROUP_MAGIC_METHODS,
		'__isset' => self::GROUP_MAGIC_METHODS,
		'__unset' => self::GROUP_MAGIC_METHODS,
		'__sleep' => self::GROUP_MAGIC_METHODS,
		'__wakeup' => self::GROUP_MAGIC_METHODS,
		'__toString' => self::GROUP_MAGIC_METHODS,
		'__invoke' => self::GROUP_MAGIC_METHODS,
		'__set_state' => self::GROUP_MAGIC_METHODS,
		'__clone' => self::GROUP_MAGIC_METHODS,
		'__debugInfo' => self::GROUP_MAGIC_METHODS,
	];

	/** @var string[] */
	public $groups = [];

	/** @var array<string, int>|null */
	private $normalizedGroups = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return array_values(Tokens::$ooScopeTokens);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $pointer
	 * @return int
	 */
	public function process(File $file, $pointer): int
	{
		$tokens = $file->getTokens();
		/** @var array{scope_closer: int, level: int} $rootScopeToken */
		$rootScopeToken = $tokens[$pointer];

		$groupsOrder = $this->getNormalizedGroups();

		$groupLastMemberPointer = $rootScopeToken['scope_opener'];
		$expectedGroup = null;
		$groupsFirstMembers = [];
		while (true) {
			$nextGroup = $this->findNextGroup($file, $groupLastMemberPointer, $rootScopeToken);
			if ($nextGroup === null) {
				break;
			}

			[$groupFirstMemberPointer, $groupLastMemberPointer, $group] = $nextGroup;

			if ($groupsOrder[$group] >= ($groupsOrder[$expectedGroup] ?? 0)) {
				$groupsFirstMembers[$group] = $groupFirstMemberPointer;
				$expectedGroup = $group;

				continue;
			}

			$fix = $file->addFixableError(
				sprintf('The placement of "%s" group is invalid.', $group),
				$groupFirstMemberPointer,
				self::CODE_INCORRECT_GROUP_ORDER
			);
			if (!$fix) {
				continue;
			}

			foreach ($groupsFirstMembers as $memberGroup => $firstMemberPointer) {
				if ($groupsOrder[$memberGroup] <= $groupsOrder[$group]) {
					continue;
				}

				$this->fixIncorrectGroupOrder(
					$file,
					$groupFirstMemberPointer,
					$groupLastMemberPointer,
					$firstMemberPointer
				);

				return $pointer - 1; // run the sniff again to fix the rest of the groups
			}
		}

		return $pointer + 1;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $pointer
	 * @param array{scope_closer: int, level: int} $rootScopeToken
	 * @return array{int, int, string}|null
	 */
	private function findNextGroup(File $file, int $pointer, array $rootScopeToken): ?array
	{
		$tokens = $file->getTokens();
		$groupTokenTypes = [T_USE, T_CONST, T_VARIABLE, T_FUNCTION];

		$currentTokenPointer = $pointer;
		while (true) {
			$currentTokenPointer = TokenHelper::findNext(
				$file,
				$groupTokenTypes,
				$currentToken['scope_closer'] ?? $currentTokenPointer + 1,
				$rootScopeToken['scope_closer']
			);
			if ($currentTokenPointer === null) {
				break;
			}

			$currentToken = $tokens[$currentTokenPointer];

			if ($currentToken['code'] === T_VARIABLE && !PropertyHelper::isProperty($file, $currentTokenPointer)) {
				continue;
			}

			if ($currentToken['level'] - $rootScopeToken['level'] !== 1) {
				continue;
			}

			$group = $this->getGroupForToken($file, $currentTokenPointer);

			if (!isset($currentGroup)) {
				$currentGroup = $group;
				$groupFirstMemberPointer = $currentTokenPointer;
			}

			if ($group !== $currentGroup) {
				break;
			}

			$groupLastMemberPointer = $currentTokenPointer;
		}

		if (!isset($currentGroup)) {
			return null;
		}

		assert(isset($groupFirstMemberPointer) === true);
		assert(isset($groupLastMemberPointer) === true);

		return [$groupFirstMemberPointer, $groupLastMemberPointer, $currentGroup];
	}

	private function getGroupForToken(File $file, int $pointer): string
	{
		$tokens = $file->getTokens();

		switch ($tokens[$pointer]['code']) {
			case T_USE:
				return self::GROUP_USES;
			case T_CONST:
				switch ($this->getVisibilityForToken($file, $pointer)) {
					case T_PUBLIC:
						return self::GROUP_PUBLIC_CONSTANTS;
					case T_PROTECTED:
						return self::GROUP_PROTECTED_CONSTANTS;
				}

				return self::GROUP_PRIVATE_CONSTANTS;
			case T_FUNCTION:
				$name = strtolower(FunctionHelper::getName($file, $pointer));
				if (array_key_exists($name, self::SPECIAL_METHODS)) {
					return self::SPECIAL_METHODS[$name];
				}

				$isStatic = $this->isMemberStatic($file, $pointer);

				if ($isStatic && $this->isStaticConstructor($file, $pointer)) {
					return self::GROUP_STATIC_CONSTRUCTORS;
				}

				switch ($this->getVisibilityForToken($file, $pointer)) {
					case T_PUBLIC:
						return $isStatic ? self::GROUP_PUBLIC_STATIC_METHODS : self::GROUP_PUBLIC_METHODS;
					case T_PROTECTED:
						return $isStatic ? self::GROUP_PROTECTED_STATIC_METHODS : self::GROUP_PROTECTED_METHODS;
				}

				return $isStatic ? self::GROUP_PRIVATE_STATIC_METHODS : self::GROUP_PRIVATE_METHODS;
			default:
				$visibility = $this->getVisibilityForToken($file, $pointer);
				$isStatic = $this->isMemberStatic($file, $pointer);

				switch ($visibility) {
					case T_PUBLIC:
						return $isStatic ? self::GROUP_PUBLIC_STATIC_PROPERTIES : self::GROUP_PUBLIC_PROPERTIES;
					case T_PROTECTED:
						return $isStatic
							? self::GROUP_PROTECTED_STATIC_PROPERTIES
							: self::GROUP_PROTECTED_PROPERTIES;
					default:
						return $isStatic ? self::GROUP_PRIVATE_STATIC_PROPERTIES : self::GROUP_PRIVATE_PROPERTIES;
				}
		}
	}

	private function getVisibilityForToken(File $file, int $pointer): int
	{
		$tokens = $file->getTokens();

		$previousPointer = TokenHelper::findPrevious(
			$file,
			array_merge(Tokens::$scopeModifiers, [T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET, T_SEMICOLON]),
			$pointer - 1
		);

		return in_array($tokens[$previousPointer]['code'], Tokens::$scopeModifiers, true) ? $tokens[$previousPointer]['code'] : T_PUBLIC;
	}

	private function isMemberStatic(File $file, int $pointer): bool
	{
		$previousPointer = TokenHelper::findPrevious($file, [T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET, T_SEMICOLON, T_STATIC], $pointer - 1);
		return $file->getTokens()[$previousPointer]['code'] === T_STATIC;
	}

	private function isStaticConstructor(File $file, int $pointer): bool
	{
		if ($this->getVisibilityForToken($file, $pointer) !== T_PUBLIC) {
			return false;
		}

		$parentClassName = $this->getParentClassName($file, $pointer);

		$returnTypeHint = FunctionHelper::findReturnTypeHint($file, $pointer);
		if ($returnTypeHint !== null) {
			return in_array($returnTypeHint->getTypeHint(), ['self', $parentClassName], true);
		}

		$returnAnnotation = FunctionHelper::findReturnAnnotation($file, $pointer);
		if ($returnAnnotation === null) {
			return false;
		}

		return in_array($returnAnnotation->getContent(), ['static', 'self', $parentClassName], true);
	}

	private function getParentClassName(File $file, int $pointer): string
	{
		$classPointer = TokenHelper::findPrevious($file, Tokens::$ooScopeTokens, $pointer - 1);
		assert($classPointer !== null);

		return ClassHelper::getName($file, $classPointer);
	}

	private function fixIncorrectGroupOrder(
		File $file,
		int $groupFirstMemberPointer,
		int $groupLastMemberPointer,
		int $nextGroupMemberPointer
	): void
	{
		$tokens = $file->getTokens();

		$previousMemberEndPointer = $this->findPreviousMemberEndPointer($file, $groupFirstMemberPointer);

		$groupStartPointer = $this->findGroupStartPointer($file, $groupFirstMemberPointer, $previousMemberEndPointer);
		$groupEndPointer = $this->findGroupEndPointer($file, $groupLastMemberPointer);

		$nextGroupMemberStartPointer = $this->findGroupStartPointer($file, $nextGroupMemberPointer);

		$file->fixer->beginChangeset();

		$content = '';
		for ($i = $groupStartPointer; $i <= $groupEndPointer; $i++) {
			$content .= $tokens[$i]['content'];
			$file->fixer->replaceToken($i, '');
		}

		$linesBetween = $this->removeBlankLinesAfterMember($file, $previousMemberEndPointer, $groupStartPointer);

		$newLines = str_repeat($file->eolChar, $linesBetween);
		$file->fixer->addContentBefore($nextGroupMemberStartPointer, $content . $newLines);

		$file->fixer->endChangeset();
	}

	private function findPreviousMemberEndPointer(File $file, int $memberPointer): int
	{
		$endTypes = [T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET, T_SEMICOLON];
		$previousMemberEndPointer = TokenHelper::findPrevious($file, $endTypes, $memberPointer - 1);
		assert($previousMemberEndPointer !== null);

		return $previousMemberEndPointer;
	}

	private function findGroupStartPointer(File $file, int $memberPointer, ?int $previousMemberEndPointer = null): int
	{
		$startPointer = DocCommentHelper::findDocCommentOpenToken($file, $memberPointer - 1);
		if ($startPointer === null) {
			if ($previousMemberEndPointer === null) {
				$previousMemberEndPointer = $this->findPreviousMemberEndPointer($file, $memberPointer);
			}

			$startPointer = TokenHelper::findNextEffective($file, $previousMemberEndPointer + 1);
			assert($startPointer !== null);
		}

		$types = [T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET, T_SEMICOLON];

		return (int) $file->findFirstOnLine($types, $startPointer, true);
	}

	private function findGroupEndPointer(File $file, int $memberPointer): int
	{
		$tokens = $file->getTokens();

		if ($tokens[$memberPointer]['code'] === T_FUNCTION && !FunctionHelper::isAbstract($file, $memberPointer)) {
			$endPointer = $tokens[$memberPointer]['scope_closer'];
		} else {
			$endPointer = TokenHelper::findNext($file, T_SEMICOLON, $memberPointer + 1);
			assert($endPointer !== null);
		}

		return $endPointer;
	}

	private function removeBlankLinesAfterMember(File $file, int $memberEndPointer, int $endPointer): int
	{
		$whitespacePointer = $memberEndPointer;

		$linesToRemove = 0;
		while (true) {
			$whitespacePointer = TokenHelper::findNext($file, T_WHITESPACE, $whitespacePointer, $endPointer);
			if ($whitespacePointer === null) {
				break;
			}

			$linesToRemove++;
			$file->fixer->replaceToken($whitespacePointer, '');
			$whitespacePointer++;
		}

		return $linesToRemove;
	}

	/**
	 * @return array<string, int>
	 */
	private function getNormalizedGroups(): array
	{
		if ($this->normalizedGroups === null) {
			$supportedGroups = [
				self::GROUP_USES,
				self::GROUP_PUBLIC_CONSTANTS,
				self::GROUP_PROTECTED_CONSTANTS,
				self::GROUP_PRIVATE_CONSTANTS,
				self::GROUP_PUBLIC_PROPERTIES,
				self::GROUP_PUBLIC_STATIC_PROPERTIES,
				self::GROUP_PROTECTED_PROPERTIES,
				self::GROUP_PROTECTED_STATIC_PROPERTIES,
				self::GROUP_PRIVATE_PROPERTIES,
				self::GROUP_PRIVATE_STATIC_PROPERTIES,
				self::GROUP_CONSTRUCTOR,
				self::GROUP_STATIC_CONSTRUCTORS,
				self::GROUP_DESTRUCTOR,
				self::GROUP_PUBLIC_METHODS,
				self::GROUP_PUBLIC_STATIC_METHODS,
				self::GROUP_PROTECTED_METHODS,
				self::GROUP_PROTECTED_STATIC_METHODS,
				self::GROUP_PRIVATE_METHODS,
				self::GROUP_PRIVATE_STATIC_METHODS,
				self::GROUP_MAGIC_METHODS,
			];

			$normalizedGroups = [];
			$order = 1;
			foreach (SniffSettingsHelper::normalizeArray($this->groups) as $groupsString) {
				/** @var string[] $groups */
				$groups = preg_split('~\\s*,\\s*~', $groupsString);
				foreach ($groups as $group) {
					$group = preg_replace('~\\s+~', ' ', $group);

					if (!in_array($group, $supportedGroups, true)) {
						throw new UnsupportedClassGroupException($group);
					}

					$normalizedGroups[$group] = $order;
				}

				$order++;
			}

			$this->normalizedGroups = $normalizedGroups;

			if (count($this->normalizedGroups) === 0) {
				$this->normalizedGroups = array_flip($supportedGroups);
			}
		}

		return $this->normalizedGroups;
	}

}
