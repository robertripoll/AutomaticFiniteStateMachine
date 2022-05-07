# Automatic Finite State Machine

A basic PHP Finite State Machine, based on [Symfony Workflows](https://github.com/symfony/workflow).

It allows the creation and usage of automatic finite state machines, that is, they apply transitions based on the current
state and the possible transitions available.

## Installation

```bash
$ composer require robertripoll/automatic-finite-state-machine
```

## Usage

### Traditional Finite State Machine

```php
<?php

use RobertRipoll\AutomaticFiniteStateMachine;
use RobertRipoll\Definition;
use RobertRipoll\FiniteStateMachine;
use RobertRipoll\State;
use RobertRipoll\StateStoreInterface;
use RobertRipoll\Transition;

// The subject on which to store the finite machine's current status
$order = new stdClass();
$order->id = 123;
$order->status = null;

// The possible finite machine statuses/nodes
$states = [
  $initialState = new State(0, 'creation'),
  $paidState = new State(1, 'paid'),
  $preparingState = new State(2, 'preparing'),
  $shippedState = new State(3, 'shipped'),
];

// The transitions between the possible finite machine nodes
$transitions = [
  new Transition($initialState, $paidState, 'pay'),
  new Transition($paidState, $preparingState, 'prepare', fn (object $order) => $paymentService->isPaid($order->id)),
  new Transition($preparingState, $shippedState, 'ship'),
];

// The definition of the finite machine
$definition = new Definition($states, $initialState, $transitions);

// The logic behind the retrieval and storage of the state
$stateStore = new class () implements StateStoreInterface {
  public function getState(object $order) {
    return $order->status;
  }

  public function setState(object $order, $newState) {
    $order->status = $newState;
  }
};

$stateMachine = new FiniteStateMachine($definition, $order, $stateStore, 'Traditional finite state machine');

if ($stateMachine->can('prepare')) {
  $stateMachine->apply('prepare');
}

```

### Automatic Finite State Machine

```php
<?php

use RobertRipoll\AutomaticFiniteStateMachine;
use RobertRipoll\Definition;
use RobertRipoll\FiniteStateMachine;
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
  new Transition($initialState, $paidState, fn (object $order) => $paymentService->isPaid($order->id)),
  new Transition($paidState, $preparingState),
  new Transition($preparingState, $shippedState, fn (object $order) => $shipmentService->isShipped($order->id)),
];

// The definition of the finite machine
$definition = new Definition($states, $initialState, $transitions);

// The logic behind the retrieval and storage of the state
$stateStore = new class () implements StateStoreInterface {
  public function getState(object $order) {
    return $order->status;
  }

  public function setState(object $order, $newState) {
    $order->status = $newState;
  }
};

$stateMachine = new AutomaticFiniteStateMachine($definition, $order, $stateStore, 'Automatic finite state machine');
$stateMachine->run();
```