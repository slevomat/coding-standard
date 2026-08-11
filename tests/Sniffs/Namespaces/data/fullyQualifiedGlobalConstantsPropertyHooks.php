<?php

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

}
