<?php

namespace RobertRipoll;

interface StateStoreInterface
{
	public function getState(object $subject);
	public function setState(object $subject, $newState): void;
}