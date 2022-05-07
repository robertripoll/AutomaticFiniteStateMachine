<?php

namespace RobertRipoll;

use RuntimeException;

class AutomaticFiniteStateMachine extends FiniteStateMachine
{
	public function getAvailableTransitions() : array
	{
		$transitions = parent::getAvailableTransitions();
		return [current($transitions)];
	}

	public function getAvailableTransition(): ?Transition
	{
		$transitions = parent::getAvailableTransitions();

		if (empty($transitions)) {
			return null;
		}

		if (count($transitions) > 1) {
			throw new RuntimeException("Only 1 transition can be available");
		}

		return current($transitions);
	}

	public function run(): bool
	{
		if ($this->getState() === null)
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