<?php // lint >= 8.4

namespace App;

class Example
{

	private int $backing = 0;

	public int $value {
		get => $this->backing;
		set => $this->backing = $value;
	}

	public int $id {
		get => 42;
	}

	public int $doubled {
		get {
			return $this->backing * 2;
		}
		set {
			$this->backing = $value;
		}
	}

	public int $clamped {
		set(int $value) {
			$this->backing = $value;
		}
	}

	public array $items = [] {
		#[\Deprecated] &get => $this->items;
	}

	public function __construct(
		public string $promoted = '' {
			set => strtoupper($value);
		},
	)
	{
	}

	public int $locked {
		final get => $this->backing;
		#[\Deprecated] final set {
			$this->backing = $value;
		}
	}

}

interface HasName
{

	public string $name { get; set; }

	public string $title {
		get;
	}

}

abstract class AbstractExample
{

	abstract public string $name { get; set; }

	abstract public string $label {
		get;
		set;
	}

}
