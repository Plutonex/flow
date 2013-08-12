<?php namespace Plutonex\Flow;

Interface PortInterface
{
	public function connect();

	public function disconnect();

	public function isConnected();

	public function attach(SocketInterface $socket);
}