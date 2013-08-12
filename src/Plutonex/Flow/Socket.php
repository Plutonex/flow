<?php namespace Plutonex\Flow;

use Plutonex\Watch\Subject;

class Socket extends Subject implements SocketInterface
{
	public $connected = false;

	public $from;

	public $to;
	
	protected $data = null;

	protected $log = array();

	protected $terminated = false;




	public function getPortName()
	{
		return $this->portName;
	}


	public function setPortName($name)
	{
		$this->portName = $name;
	}

	


	public function connectTo( PortInterface $to)
	{
		$this->to = $to;
		$this->log('ConnectTo port set to ' . $this->to->getName());

	}


	public function connectFrom( PortInterface $from)
	{
		$this->from = $from;
		$this->log('ConnectFrom port set to ' . $this->from->getName());
		
	}



	public function isConnected()
	{
		return $this->connected;
	}

	public function isTerminated()
	{
		return $this->terminated;
	}

	public function terminate()
	{
		$this->log('###  PORT '.$this->getPortName().' TERMINATED  ###');
		$this->terminated = true;
		
		if($this->to)
		{
			$this->to->terminate();
		}

		$this->disconnect();

	}



	public function connect()
	{
		
		$this->connected = true;
		$this->log('-###- Connection Activated on port ' . $this->getPortName());
	
		$this->setState('onConnect');

		if($this->from)
		{
			$this->from->log('-###- Connection active between ports ' . $this->from->getName(). ' <=> ' . $this->getPortName());
		}

		if($this->to)
		{
			$this->log('trying to make connection to '. $this->to->getName());
			$this->to->connect();
		}

		$this->setState('connected');
	}


	public function setState($state)
	{
		if(! $this->isTerminated())
		{
			$this->log('switch state to '. $state);
			parent::setState($state);
		}
	}


	public function notify()
	{
		parent::notify();
		$this->log('Number of oberservers run under state '. $this->getState() . ' : '. $this->numObserversSent($this->getState()));
		
		if($this->numObserversSent($this->getState()))
		{
			if($this->from)
			{
				$this->from->push($this->getData());
			}
			
		}
		
	}



	public function disconnect($state = null)
	{
	
		if(! $this->isConnected())
		{
			$this->log('already disconnected');
			return;
		}
		
		if(! $this->isTerminated())
		{
			$this->send();
		}
		
		if($this->to)
		{
			$this->to->disconnect($state);
		}

		if($this->connected == true)
		{
			if(!is_null($state))
			{
				$this->setState($state);
			}

			$this->log('##### disconnected port '. $this->getPortName());
			$this->connected = false;
		}
		
	}



	public function setData($data, $state = null)
	{
		if(!is_null($data))
		{
			$this->data = $data;
		
			if(! is_null($state))
			{
				$this->log('wrote data ');
				$this->setState($state);

			} else
			{
				$this->log('data updated');
			}

			$this->updateParentData();
		}
	
	}

	protected function updateParentData()
	{
		if($this->from)
		{
			$this->log('returning data to port '. $this->from->getName());

			$this->from->push($this->getData());
		}
	}


	public function getData()
	{
		return $this->data;
	}


	public function send()
	{
		if(! $this->to)
		{
			return;
		}else
		{
			if($this->to->isConnected())
			{
				$this->log('sending data to '. $this->to->getName());
				$this->to->input($this->getData());
			}
		}
	}


	public function log($status, $prefix = true)
	{
		if($prefix)
		{
			$status = $this->getPortName() . ': '. $status;
		}

		array_push($this->log, $status);

		//push log to parent if it exists
		if(! empty($this->from))
		{
			$this->from->pushLog($status);
		}
	}


	public function getLogs($string = false)
	{
		if($string)
		{
			return implode(PHP_EOL, $this->log);
		}

		return $this->log;

	}
	
}