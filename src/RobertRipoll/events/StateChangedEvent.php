<?php

namespace RobertRipoll\Events;

use RobertRipoll\FiniteStateMachine;
use RobertRipoll\State;

class StateChangedEvent
{
	private FiniteStateMachine $stateMachine;
	private ?State $oldState;
	private State $newState;
	private object $subject;

	public function __construct(FiniteStateMachine $stateMachine, ?State $oldState, State $newState)
	{
		$this->stateMachine = $stateMachine;
		$this->oldState = $oldState;
		$this->newState = $newState;
		$this->subject = $stateMachine->getSubject();
	}

	/**
	 * @return FiniteStateMachine
	 */
	public function getStateMachine(): FiniteStateMachine
	{
		return $this->stateMachine;
	}

	public function hasOldState(): bool
	{
		return (bool)$this->oldState;
	}

	/**
	 * @return State|null
	 */
	public function getOldState(): ?State
	{
		return $this->oldState;
	}

	/**
	 * @return State
	 */
	public function getNewState(): State
	{
		return $this->newState;
	}

	/**
	 * @return object
	 */
	public function getSubject(): object
	{
		return $this->subject;
	}
}