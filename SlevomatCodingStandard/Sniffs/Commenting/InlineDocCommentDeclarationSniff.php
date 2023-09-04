<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function in_array;
use function preg_match;
use function sprintf;
use function substr;
use const T_AS;
use const T_ATTRIBUTE;
use const T_CLOSURE;
use const T_COALESCE_EQUAL;
use const T_COMMENT;
use const T_CONST;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_EQUAL;
use const T_FINAL;
use const T_FN;
use const T_FOREACH;
use const T_LIST;
use const T_OPEN_SHORT_ARRAY;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_READONLY;
use const T_RETURN;
use const T_SEMICOLON;
use const T_STATIC;
use const T_VARIABLE;
use const T_WHILE;

class InlineDocCommentDeclarationSniff implements Sniff
{

	public const CODE_INVALID_FORMAT = 'InvalidFormat';
	public const CODE_INVALID_COMMENT_TYPE = 'InvalidCommentType';
	public const CODE_MISSING_VARIABLE = 'MissingVariable';
	public const CODE_NO_ASSIGNMENT = 'NoAssignment';

	/** @var bool */
	public $allowDocCommentAboveReturn = false;

	/** @var bool */
	public $allowAboveNonAssignment = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
			T_COMMENT,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $commentOpenPointer
	 */
	public function process(File $phpcsFile, $commentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$commentClosePointer = $tokens[$commentOpenPointer]['code'] === T_COMMENT
			? $commentOpenPointer
			: $tokens[$commentOpenPointer]['comment_closer'];

		$pointerAfterCommentClosePointer = TokenHelper::findNextEffective($phpcsFile, $commentClosePointer + 1);
		if ($pointerAfterCommentClosePointer !== null) {
			do {
				if ($tokens[$pointerAfterCommentClosePointer]['code'] !== T_ATTRIBUTE) {
					break;
				}

				$pointerAfterCommentClosePointer = TokenHelper::findNextEffective(
					$phpcsFile,
					$tokens[$pointerAfterCommentClosePointer]['attribute_closer'] + 1
				);
			} while (true);

			if (in_array(
				$tokens[$pointerAfterCommentClosePointer]['code'],
				[T_PRIVATE, T_PROTECTED, T_PUBLIC, T_READONLY, T_FINAL, T_CONST],
				true
			)) {
				return;
			}

			if ($tokens[$pointerAfterCommentClosePointer]['code'] === T_STATIC) {
				$pointerAfterStatic = TokenHelper::findNextEffective($phpcsFile, $pointerAfterCommentClosePointer + 1);
				if (in_array($tokens[$pointerAfterStatic]['code'], [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_READONLY], true)) {
					return;
				}

				if ($tokens[$pointerAfterStatic]['code'] === T_VARIABLE && PropertyHelper::isProperty($phpcsFile, $pointerAfterStatic)) {
					return;
				}
			}
		}

		if ($tokens[$commentOpenPointer]['code'] === T_COMMENT) {
			$this->checkCommentType($phpcsFile, $commentOpenPointer);
			return;
		}

		/** @var list<Annotation<VarTagValueNode>> $annotations */
		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $commentOpenPointer, '@var');

		if ($annotations === []) {
			return;
		}

		if ($this->allowDocCommentAboveReturn) {
			$pointerAfterCommentClosePointer = TokenHelper::findNextEffective($phpcsFile, $commentClosePointer + 1);
			if ($tokens[$pointerAfterCommentClosePointer]['code'] === T_RETURN) {
				return;
			}
		}

		$this->checkFormat($phpcsFile, $annotations);
		$this->checkVariable($phpcsFile, $annotations, $commentOpenPointer, $commentClosePointer);
	}

	private function checkCommentType(File $phpcsFile, int $commentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (preg_match('~^/\*\\s*@var\\s+~', $tokens[$commentOpenPointer]['content']) === 0) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Invalid comment type /* */ for inline documentation comment, use /** */.',
			$commentOpenPointer,
			self::CODE_INVALID_COMMENT_TYPE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($commentOpenPointer, sprintf('/**%s', substr($tokens[$commentOpenPointer]['content'], 2)));
		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param list<Annotation<VarTagValueNode>> $annotations
	 */
	private function checkFormat(File $phpcsFile, array $annotations): void
	{
		foreach ($annotations as $annotation) {
			if (!$annotation->isInvalid() && $annotation->getValue()->variableName !== '') {
				continue;
			}

			$variableName = '$variableName';

			$annotationContent = (string) $annotation->getValue();

			$type = null;

			if (
				$annotationContent !== ''
				&& preg_match('~(\$\w+)(?:\s+(.+))?$~i', $annotationContent, $matches) === 1
			) {
				$variableName = $matches[1];
				$type = $matches[2] ?? null;
			}

			// It may be description when it contains whitespaces
			$isFixable = $type !== null && preg_match('~\s~', $type) === 0;

			if (!$isFixable) {
				$phpcsFile->addError(
					sprintf(
						'Invalid inline documentation comment format "@var %1$s", expected "@var type %2$s Optional description".',
						$annotationContent,
						$variableName
					),
					$annotation->getStartPointer(),
					self::CODE_INVALID_FORMAT
				);

				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Invalid inline documentation comment format "@var %1$s", expected "@var %2$s %3$s".',
					$annotationContent,
					$type,
					$variableName
				),
				$annotation->getStartPointer(),
				self::CODE_INVALID_FORMAT
			);

			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			$phpcsFile->fixer->addContent(
				$annotation->getStartPointer(),
				sprintf(
					' %s %s ',
					$type,
					$variableName
				)
			);

			FixerHelper::removeBetweenIncluding($phpcsFile, $annotation->getStartPointer() + 1, $annotation->getEndPointer());

			$phpcsFile->fixer->endChangeset();
		}
	}

	/**
	 * @param list<Annotation<VarTagValueNode>> $annotations
	 */
	private function checkVariable(File $phpcsFile, array $annotations, int $docCommentOpenerPointer, int $docCommentCloserPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$checkedTokens = [T_VARIABLE, T_FOREACH, T_WHILE, T_LIST, T_OPEN_SHORT_ARRAY, T_CLOSURE, T_FN];

		$variableNames = [];

		foreach ($annotations as $variableAnnotation) {
			if ($variableAnnotation->isInvalid()) {
				continue;
			}

			$variableName = $variableAnnotation->getValue()->variableName;
			if ($variableName === '') {
				continue;
			}

			$variableNames[] = $variableName;
		}

		$improveCodePointer = function (int $codePointer) use ($phpcsFile, $tokens, $checkedTokens, $variableNames): int {
			$shouldSearchClosure = false;

			if (!in_array($tokens[$codePointer]['code'], $checkedTokens, true)) {
				$shouldSearchClosure = true;
			} elseif (
				$tokens[$codePointer]['code'] === T_VARIABLE
				&& (
					!$this->isAssignment($phpcsFile, $codePointer)
					|| !in_array($tokens[$codePointer]['content'], $variableNames, true)
				)
			) {
				$shouldSearchClosure = true;
			}

			if (!$shouldSearchClosure) {
				return $codePointer;
			}

			$closurePointer = TokenHelper::findNext($phpcsFile, [T_CLOSURE, T_FN], $codePointer + 1);
			if ($closurePointer !== null && $tokens[$codePointer]['line'] === $tokens[$closurePointer]['line']) {
				return $closurePointer;
			}

			return $codePointer;
		};

		$firstPointerOnNextLine = TokenHelper::findFirstNonWhitespaceOnNextLine($phpcsFile, $docCommentCloserPointer);

		$codePointerAfter = $firstPointerOnNextLine;
		while ($codePointerAfter !== null && $tokens[$codePointerAfter]['code'] === T_DOC_COMMENT_OPEN_TAG) {
			$codePointerAfter = TokenHelper::findFirstNonWhitespaceOnNextLine($phpcsFile, $codePointerAfter + 1);
		}

		if ($codePointerAfter !== null) {
			if ($tokens[$codePointerAfter]['code'] === T_STATIC) {
				$codePointerAfter = TokenHelper::findNextEffective($phpcsFile, $codePointerAfter + 1);
			}

			$codePointerAfter = $improveCodePointer($codePointerAfter);
		}

		$codePointerBefore = TokenHelper::findFirstNonWhitespaceOnPreviousLine($phpcsFile, $docCommentOpenerPointer);
		while ($codePointerBefore !== null && $tokens[$codePointerBefore]['code'] === T_DOC_COMMENT_OPEN_TAG) {
			$codePointerBefore = TokenHelper::findFirstNonWhitespaceOnPreviousLine($phpcsFile, $codePointerBefore - 1);
		}

		if ($codePointerBefore !== null) {
			$codePointerBefore = $improveCodePointer($codePointerBefore);
		}

		foreach ($annotations as $variableAnnotation) {
			if ($variableAnnotation->isInvalid()) {
				continue;
			}

			$variableName = $variableAnnotation->getValue()->variableName;
			if ($variableName === '') {
				continue;
			}

			$missingVariableErrorParameters = [
				sprintf('Missing variable %s before or after the documentation comment.', $variableName),
				$docCommentOpenerPointer,
				self::CODE_MISSING_VARIABLE,
			];

			$noAssignmentErrorParameters = [
				sprintf('No assignment to %s variable before or after the documentation comment.', $variableName),
				$docCommentOpenerPointer,
				self::CODE_NO_ASSIGNMENT,
			];

			if ($this->allowAboveNonAssignment && $firstPointerOnNextLine !== null) {
				for ($i = $firstPointerOnNextLine; $i < count($tokens); $i++) {
					if ($tokens[$i]['line'] > $tokens[$firstPointerOnNextLine]['line']) {
						break;
					}

					if ($tokens[$i]['code'] !== T_VARIABLE) {
						continue;
					}

					if ($tokens[$i]['content'] === $variableName) {
						return;
					}
				}
			}

			foreach ([1 => $codePointerBefore, 2 => $codePointerAfter] as $tryNo => $codePointer) {
				if ($codePointer === null || !in_array($tokens[$codePointer]['code'], $checkedTokens, true)) {
					if ($tryNo === 2) {
						$phpcsFile->addError(...$missingVariableErrorParameters);
					}

					continue;
				}

				if ($tokens[$codePointer]['code'] === T_VARIABLE) {
					if ($tokens[$codePointer]['content'] !== '$this' && !$this->isAssignment($phpcsFile, $codePointer)) {
						if ($tryNo === 2) {
							$phpcsFile->addError(...$noAssignmentErrorParameters);
						}

						continue;
					}

					if ($variableName !== $tokens[$codePointer]['content']) {
						if ($tryNo === 2) {
							$phpcsFile->addError(...$missingVariableErrorParameters);
						}

						continue;
					}
				} elseif ($tokens[$codePointer]['code'] === T_LIST) {
					$listParenthesisOpener = TokenHelper::findNextEffective($phpcsFile, $codePointer + 1);

					$variablePointerInList = TokenHelper::findNextContent(
						$phpcsFile,
						T_VARIABLE,
						$variableName,
						$listParenthesisOpener + 1,
						$tokens[$listParenthesisOpener]['parenthesis_closer']
					);
					if ($variablePointerInList === null) {
						if ($tryNo === 2) {
							$phpcsFile->addError(...$missingVariableErrorParameters);
						}

						continue;
					}

				} elseif ($tokens[$codePointer]['code'] === T_OPEN_SHORT_ARRAY) {
					$pointerAfterList = TokenHelper::findNextEffective($phpcsFile, $tokens[$codePointer]['bracket_closer'] + 1);
					if ($tokens[$pointerAfterList]['code'] !== T_EQUAL) {
						if ($tryNo === 2) {
							$phpcsFile->addError(...$noAssignmentErrorParameters);
						}

						continue;
					}

					$variablePointerInList = TokenHelper::findNextContent(
						$phpcsFile,
						T_VARIABLE,
						$variableName,
						$codePointer + 1,
						$tokens[$codePointer]['bracket_closer']
					);
					if ($variablePointerInList === null) {
						if ($tryNo === 2) {
							$phpcsFile->addError(...$missingVariableErrorParameters);
						}

						continue;
					}

				} elseif (in_array($tokens[$codePointer]['code'], [T_CLOSURE, T_FN], true)) {
					$parameterPointer = TokenHelper::findNextContent(
						$phpcsFile,
						T_VARIABLE,
						$variableName,
						$tokens[$codePointer]['parenthesis_opener'] + 1,
						$tokens[$codePointer]['parenthesis_closer']
					);
					if ($parameterPointer === null) {
						if ($tryNo === 2) {
							$phpcsFile->addError(...$missingVariableErrorParameters);
						}

						continue;
					}

				} else {
					if ($tokens[$codePointer]['code'] === T_WHILE) {
						$variablePointerInWhile = TokenHelper::findNextContent(
							$phpcsFile,
							T_VARIABLE,
							$variableName,
							$tokens[$codePointer]['parenthesis_opener'] + 1,
							$tokens[$codePointer]['parenthesis_closer']
						);
						if ($variablePointerInWhile === null) {
							if ($tryNo === 2) {
								$phpcsFile->addError(...$missingVariableErrorParameters);
							}

							continue;
						}

						$pointerAfterVariableInWhile = TokenHelper::findNextEffective($phpcsFile, $variablePointerInWhile + 1);
						if ($tokens[$pointerAfterVariableInWhile]['code'] !== T_EQUAL) {
							if ($tryNo === 2) {
								$phpcsFile->addError(...$noAssignmentErrorParameters);
							}

							continue;
						}
					} else {
						$asPointer = TokenHelper::findNext(
							$phpcsFile,
							T_AS,
							$tokens[$codePointer]['parenthesis_opener'] + 1,
							$tokens[$codePointer]['parenthesis_closer']
						);
						$variablePointerInForeach = TokenHelper::findNextContent(
							$phpcsFile,
							T_VARIABLE,
							$variableName,
							$asPointer + 1,
							$tokens[$codePointer]['parenthesis_closer']
						);
						if ($variablePointerInForeach === null) {
							if ($tryNo === 2) {
								$phpcsFile->addError(...$missingVariableErrorParameters);
							}

							continue;
						}
					}
				}

				// No error, don't check second $codePointer
				continue 2;
			}
		}
	}

	private function isAssignment(File $phpcsFile, int $pointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$pointerAfterVariable = TokenHelper::findNextEffective($phpcsFile, $pointer + 1);
		if ($tokens[$pointerAfterVariable]['code'] === T_SEMICOLON) {
			$pointerBeforeVariable = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);
			return $tokens[$pointerBeforeVariable]['code'] === T_STATIC;
		}

		return in_array($tokens[$pointerAfterVariable]['code'], [T_EQUAL, T_COALESCE_EQUAL], true);
	}

}
