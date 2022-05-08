<?php

namespace RobertRipoll;

use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use RobertRipoll\events\ApplicabilityCheckedEvent;
use RobertRipoll\events\StateChangedEvent;
use RuntimeException;

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
	/** @var ?EventDispatcherInterface */
	protected ?EventDispatcherInterface $eventDispatcher;
	/** @var ?string Name of the finite state machine */
	protected ?string $name;

	public function __construct(
		Definition               $definition,
		object                   $subject,
		StateStoreInterface      $stateStore,
		EventDispatcherInterface $eventDispatcher = null,
		?string                  $name = null
	)
	{
		$this->definition = $definition;
		$this->subject = $subject;
		$this->stateStore = $stateStore;
		$this->eventDispatcher = $eventDispatcher;
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

	public function hasState(): bool
	{
		return (bool)$this->getState();
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

	public function can(string $transitionName): bool
	{
		$transition = $this->definition->getTransitions()[$transitionName] ?: null;

		return $transition && $transition->isApplicable($this->subject);
	}

	public function apply(string $transitionName): void
	{
		$transition = $this->definition->getTransitions()[$transitionName] ?: null;

		if (!$transition) {
			throw new InvalidArgumentException("No transition with name $transitionName exists");
		}

		$isApplicable = $transition->isApplicable($this->subject);

		if ($this->eventDispatcher)
		{
			/** @var ApplicabilityCheckedEvent $event */
			$event = $this->eventDispatcher->dispatch(new ApplicabilityCheckedEvent($this, $transition, $isApplicable));
			$isApplicable = $event->isApplicable();
		}

		if (!$isApplicable) {
			throw new RuntimeException("Transition with name $transitionName cannot be applied");
		}

		$this->setState($transition->getTo());
	}

	protected function setState(State $state): void
	{
		$oldState = $this->getState();
		$this->stateStore->setState($this->subject, $state);

		if ($this->eventDispatcher) {
			$this->eventDispatcher->dispatch(new StateChangedEvent($this, $oldState, $state));
		}
	}

	public function getAvailableTransitions(): array
	{
		$transitions = $this->definition->getTransitions();
		$result = [];

		foreach ($transitions as $transition)
		{
			if ($transition->getFrom()->getValue() == $this->getState()->getName())
			{
				if ($transition->isApplicable($this->subject)) {
					$result[$transition->getName()] = $transition;
				}
			}
		}

		return $result;
	}
}