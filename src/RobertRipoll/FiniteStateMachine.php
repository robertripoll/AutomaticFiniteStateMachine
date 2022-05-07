<?php

namespace RobertRipoll;

/**
 *
 */
class FiniteStateMachine
{
	/** @var Definition Finite state machine's definition */
	protected Definition $definition;
	/** @var object Object on which to apply the current state */
	protected object $subject;
	/** @var StateStoreInterface Contains the logic for getting and storing the state into the subject */
	protected StateStoreInterface $stateStore;
	/** @var ?string Name of the finite state machine */
	protected ?string $name;

	public function __construct(Definition $definition, object $subject, StateStoreInterface $stateStore, ?string $name = null)
	{
		$this->definition = $definition;
		$this->subject = $subject;
		$this->stateStore = $stateStore;
		$this->name = $name;
	}

	public function getDefinition(): Definition
	{
		return $this->definition;
	}

	public function getSubject(): object
	{
		return $this->subject;
	}

	public function getStateStore(): StateStoreInterface
	{
		return $this->stateStore;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function getState(): ?State
	{
		$states = $this->definition->getStates();
		$state = $this->stateStore->getState($this->subject);

		if ($state === null) {
			return null;
		}

		return $states[$state] ?: null;
	}

	protected function setState(State $state)
	{
		$this->stateStore->setState($this->subject, $state);
	}

	public function getAvailableTransitions(): array
	{
		$transitions = $this->definition->getTransitions();
		$availableTransitions = $transitions[$this->getState()->getValue()];

		$result = [];

		foreach ($availableTransitions as $transition)
		{
			if ($transition->isApplicable($this->subject)) {
				$result[] = $transition;
			}
		}

		return $result;
	}
}