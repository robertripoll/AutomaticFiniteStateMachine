<?php

namespace RobertRipoll;

use Closure;

class Transition
{
	private State $from;
	private State $to;
	private ?Closure $when;
	private string $name;

	public function __construct(State $from, State $to, string $name = null, ?Closure $when = null)
	{
		$this->from = $from;
		$this->to = $to;
		$this->when = $when;
		$this->name = $name;
	}

	public function getFrom(): State
	{
		return $this->from;
	}

	public function getTo(): State
	{
		return $this->to;
	}

	public function getWhen(): ?Closure
	{
		return $this->when;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function isApplicable(object $subject): bool
	{
		if (!($when = $this->when)) {
			return true;
		}

		return $when($subject, $this);
	}
}