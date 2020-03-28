<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use Closure;
use PHP_CodeSniffer\Files\File;
use function array_key_exists;

/**
 * Use for caching some value based on phpcsFile version
 *
 * @internal
 */
final class SniffLocalCache
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
	 * @var array<string, mixed>
	 */
	private $cache = [];

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
	 * @param File $phpcsFile
	 * @param Closure $lazyValue
	 * @return mixed
	 */
	public function getAndSetIfNotCached(File $phpcsFile, Closure $lazyValue)
	{
		$this->setIfNotCached($phpcsFile, $lazyValue);

		return $this->get($phpcsFile);
	}

	private function setIfNotCached(File $phpcsFile, Closure $lazyValue): void
	{
		if ($this->has($phpcsFile)) {
			return;
		}

		$this->set($phpcsFile, $lazyValue());
	}

	private function has(File $phpcsFile): bool
	{
		$key = $this->key($phpcsFile);

		return array_key_exists($key, $this->cache);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
	 * @param File $phpcsFile
	 * @return mixed
	 */
	private function get(File $phpcsFile)
	{
		return $this->cache[$this->key($phpcsFile) ] ?? null;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
	 * @param File $phpcsFile
	 * @param mixed $value
	 */
	private function set(File $phpcsFile, $value): void
	{
		$key = $this->key($phpcsFile);
		$this->cache[$key] = $value;
		$this->removeOldCache($phpcsFile);
	}

	private function key(File $phpcsFile): string
	{
		$cacheKey = $phpcsFile->getFilename();
		$fixerLoops = $phpcsFile->fixer !== null ? $phpcsFile->fixer->loops : 0;

		return $cacheKey . '-loop' . $fixerLoops;
	}

	private function previousKey(File $phpcsFile): ?string
	{
		$cacheKey = $phpcsFile->getFilename();
		$fixerLoops = $phpcsFile->fixer !== null ? $phpcsFile->fixer->loops : 0;
		if ($fixerLoops === 0) {
			return null;
		}

		return $cacheKey . '-loop' . ($fixerLoops - 1);
	}

	private function removeOldCache(File $phpcsFile): void
	{
		$key = $this->previousKey($phpcsFile);
		if ($key === null) {
			return;
		}

		unset($this->cache[$key]);
	}

}
