<?php

namespace RobertRipoll\Events;

interface FiniteStateMachineEventInterface
{
	public function getEventName(): string;
}