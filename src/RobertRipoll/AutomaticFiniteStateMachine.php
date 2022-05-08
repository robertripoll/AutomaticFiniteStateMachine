<?php

namespace RobertRipoll;

use RuntimeException;

class AutomaticFiniteStateMachine extends FiniteStateMachine
{
	public function can(string $transitionName): bool
	{
		// Transitions cannot be applied manually since they are applied automatically :)
		return false;
	}

	public function apply(string $transitionName): void
	{
		// Transitions cannot be applied manually since they are applied automatically :)
	}

	public function getAvailableTransition(): ?Transition
	{
		$transitions = parent::getAvailableTransitions();

		if (empty($transitions)) {
			return null;
		}

		if (count($transitions) > 1) {
			throw new RuntimeException("Only 1 transition can be available at the same time");
		}

		return current($transitions);
	}

	public function run(): bool
	{
		if (!$this->hasState())
		{
			$this->setState($this->definition->getInitialState());
			return true;
		}

		if (!($transition = $this->getAvailableTransition())) {
			return false;
		}

		$this->setState($transition->getTo());
		return true;
	}
}