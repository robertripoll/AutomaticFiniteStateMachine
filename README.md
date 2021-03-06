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
  public function getState(object $subject) {
    return $subject->status;
  }

  public function setState(object $subject, $newState): void {
    $subject->status = $newState;
  }
};

$stateMachine = new FiniteStateMachine($definition, $order, $stateStore, null, 'Traditional finite state machine');

if ($stateMachine->can('prepare')) {
  $stateMachine->apply('prepare');
}
```

### Automatic Finite State Machine

```php
<?php

use RobertRipoll\AutomaticFiniteStateMachine;
use RobertRipoll\Definition;
use RobertRipoll\Events\FiniteStateMachineEventInterface;
use RobertRipoll\Events\StateChangedEvent;
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
  new Transition($initialState, $paidState, 'pay', fn (object $order) => $paymentService->isPaid($order->id)),
  new Transition($paidState, $preparingState, 'prepare'),
  new Transition($preparingState, $shippedState, 'ship', fn (object $order) => $shipmentService->isShipped($order->id)),
];

// The definition of the finite machine
$definition = new Definition($states, $initialState, $transitions);

// The logic behind the retrieval and storage of the state
$stateStore = new class () implements StateStoreInterface {
  public function getState(object $subject) {
    return $subject->status;
  }

  public function setState(object $subject, $newState): void {
    $subject->status = $newState;
  }
};

$eventDispatcher = new class ($logService) implements EventDispatcherInterface {
  private LogService $logService;

  public function __construct(LogService $logService) {
    $this->logService = $logService;
  }

  public function dispatch(object $event)
  {
    /** @var FiniteStateMachineEventInterface $event */

    if ($event->getEventName() == StateChangedEvent::EVENT_NAME)
    {
      /** @var StateChangedEvent $event */
      $oldState = $event->hasOldState() ? $event->getOldState()->getValue() : 'null';
      $logService->log("Subject state changed from $oldState to {$event->getNewState()->getValue()}");
    }
  }
};

$stateMachine = new AutomaticFiniteStateMachine($definition, $order, $stateStore, $eventDispatcher, 'Automatic finite state machine');
$stateMachine->run();
```