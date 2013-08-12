<?php namespace Plutonex\Flow;


Interface SocketInterface
{

	// public function getId();

	public function isConnected();

	public function connect();

	public function disconnect();
}