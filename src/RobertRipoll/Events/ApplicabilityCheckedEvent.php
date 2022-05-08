<?php

namespace RobertRipoll\Events;

use RobertRipoll\FiniteStateMachine;
use RobertRipoll\Transition;

class ApplicabilityCheckedEvent implements FiniteStateMachineEventInterface
{
	public const EVENT_NAME = 'applicability.checked';

	private FiniteStateMachine $stateMachine;
	private Transition $transition;
	private bool $isApplicable;
	private object $subject;

	public function __construct(FiniteStateMachine $stateMachine, Transition $transition, bool $isApplicable)
	{
		$this->stateMachine = $stateMachine;
		$this->transition = $transition;
		$this->isApplicable = $isApplicable;
		$this->subject = $stateMachine->getSubject();
	}

	public function getEventName() : string
	{
		return self::EVENT_NAME;
	}

	/**
	 * @return FiniteStateMachine
	 */
	public function getStateMachine(): FiniteStateMachine
	{
		return $this->stateMachine;
	}

	/**
	 * @return Transition
	 */
	public function getTransition(): Transition
	{
		return $this->transition;
	}

	/**
	 * @return bool
	 */
	public function isApplicable(): bool
	{
		return $this->isApplicable;
	}

	/**
	 * @param bool $isApplicable
	 */
	public function setIsApplicable(bool $isApplicable): void
	{
		$this->isApplicable = $isApplicable;
	}

	/**
	 * @return object
	 */
	public function getSubject(): object
	{
		return $this->subject;
	}
}