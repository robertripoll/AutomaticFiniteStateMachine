# Automatic Finite State Machine

A basic PHP Finite State Machine, based on [Symfony Workflows](https://github.com/symfony/workflow).

It allows the creation and use of automatic finite state machines, that is, they apply transitions based on the current
state and the possible transitions available.

## Installation

```bash
$ composer require robertripoll/automatic-finite-state-machine
```

## Usage

```php
<?php

use RobertRipoll\AutomaticFiniteStateMachine;
use RobertRipoll\Definition;
use RobertRipoll\State;
use RobertRipoll\StateStoreInterface;
use RobertRipoll\Transition;

// The subject on which to store the finite machine's current status
$order = new stdClass();
$order->id = 123;
$order->status = null;

// The possible finite machine statuses/nodes
$states = [
	$initialState = new State(0, 'created'),
	$paidState = new State(1, 'paid'),
	$preparingState = new State(2, 'preparing'),
	$shippedState = new State(3, 'shipped'),
];

// The transitions between the possible finite machine nodes
$transitions = [
	new Transition($initialState, $paidState, fn (Transition $transition, object $order) => $paymentService->isPaid($order->id)),
	new Transition($paidState, $preparingState),
	new Transition($preparingState, $shippedState, fn (Transition $transition, object $order) => $shipmentService->isShipped($order->id)),
];

// The definition of the finite machine
$definition = new Definition($states, $initialState, $transitions);

// The logic behind the retrieval and storage of the state
$stateStore = new class () implements StateStoreInterface {
	public function getState(object $subject) {
		return $subject->status;
	}

	public function setState(object $subject, $newState) {
		$subject->status = $newState;
	}
};

$machine = new AutomaticFiniteStateMachine($definition, $order, $stateStore, 'Automatic finite state machine');
$machine->run();
```