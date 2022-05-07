<?php

namespace RobertRipoll;

use InvalidArgumentException;

/**
 *
 */
class Definition
{
	/** @var array Graph nodes */
	private array $states;
	/** @var State Initial graph node */
	private State $initialState;
	/** @var array Graph edges (transitions between the graph nodes) */
	private array $transitions;

	/**
	 * @param State[] $states Possible graph nodes
	 * @param State $initialState Initial graph node
	 * @param Transition[] $transitions Graph edges (transitions between the graph nodes)
	 */
	public function __construct(array $states, State $initialState, array $transitions)
	{
		$this->states = $this->transitions = [];

		foreach ($states as $state) {
			$this->states[$state->getValue()] = $state;
		}

		if (!array_key_exists($initialState->getValue(), $this->states)) {
			throw new InvalidArgumentException('$initialState must be a member of $states');
		}

		$this->initialState = $initialState;

		foreach ($transitions as $index => $transition)
		{
			$name = $transition->getName() ?: "#$index";
			$from = $transition->getFrom()->getValue();
			$to = $transition->getTo()->getValue();

			if (!array_key_exists($from, $this->states)) {
				throw new InvalidArgumentException('$from used in transition ' . $name . ' is not a member of $states');
			}

			if (!array_key_exists($to, $this->states)) {
				throw new InvalidArgumentException('$to used in transition ' . $name . ' is not a member of $states');
			}

			$this->transitions[$from][] = $transition;
		}
	}

	/**
	 * Returns the graph nodes.
	 *
	 * @return array Graph nodes
	 */
	public function getStates(): array
	{
		return $this->states;
	}

	/**
	 * Returns the initial graph node.
	 *
	 * @return State Initial graph node
	 */
	public function getInitialState(): State
	{
		return $this->initialState;
	}

	/**
	 * Returns the graph transitions (from node A to node B).
	 *
	 * @return Transition[] Graph transitions
	 */
	public function getTransitions(): array
	{
		return $this->transitions;
	}
}