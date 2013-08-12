<?php namespace Plutonex\Flow;

use Plutonex\Watch\Subject;

Class Port implements PortInterface
{

	protected $socket;


	public function __construct($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}


	public function connect()
	{
		if (!$this->socket)
		{
            throw new \RuntimeException("No socket to connect");
        }

   		$this->socket->connect();
	}

	public function connectTo(PortInterface $port)
	{
		$port->connectFrom($this);
		$this->socket->connectTo($port);
	}


	public function connectFrom(PortInterface $port)
	{
		$this->socket->connectFrom($port);
	}



	public function disconnect($state = 'onDisconnect')
	{
		if (!$this->socket)
		{
            return;
        }

        if($this->isConnected())
        {
        	$this->socket->disconnect($state);
        }
        
	}


	public function terminate()
	{
		$this->socket->terminate();
	}



	public function isConnected()
	{
		if(! $this->socket)
		{
			return false;
		}

		return $this->socket->isConnected();
	}


	public function attach(SocketInterface $socket)
	{
		$this->socket = $socket;
		$this->socket->setPortName($this->getName());
		//$this->socket->connectFrom($this);
	}


	public function input($data)
	{
		if(!$this->socket)
		{
			throw new \Exception("No socket connected");
		}
		$this->log('onReady');
		$this->socket->setData($data, 'onReady');
	}



	public function output()
	{
		$this->disconnect();

		return $this->socket->getData();
	}

	public function push($data)
	{
		$this->socket->setData($data);
	}


	public function send()
	{
		$this->socket->send();
	}
	

	public function getLogs($string = null)
	{
		return $this->socket->getLogs($string);
	}


	public function log($status)
	{
		$this->socket->log($status);
	}


	public function pushLog($status)
	{
		$this->socket->log($status, false);
	}

}