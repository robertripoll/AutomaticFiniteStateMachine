<?php

namespace RobertRipoll;

class State
{
	/** @var int|string $value */
	private $value;
	private ?string $name;

	public function __construct($value, ?string $name = null)
	{
		$this->value = $value;
		$this->name = $name;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getName(): ?string
	{
		return $this->name;
	}
}