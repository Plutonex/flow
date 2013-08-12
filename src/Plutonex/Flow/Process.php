<?php namespace Plutonex\Flow;

use Plutonex\Watch\Subject;
use Plutonex\Watch\ObserverInterface;

abstract class Process extends Subject implements ProcessInterface
{

	protected $extensions = array();

	protected $sequence;


	public function __construct(array $sequence, $auto = true)
	{
		$this->sequence = $sequence;

		if($auto)
		{
			$this->start();	
		}
	}


	public function start()
	{
		$this->setState("start");
		$this->run();
		$this->stop();
	}


	public function stop()
	{
		$this->setState("stop");
	}



	public function run()
	{
		foreach($sequence as $state)
		{
			$this->setState($state);
		}
	}



	public function extend($method, $extension)
	{
		if(is_callable($extension))
		{
			$this->extensions[$method] = $extension;
		}
	}


	public function __call($method, $args)
	{
		if(isset($this->extensions[$method]))
		{
			return call_user_func_array($this->extensions[$method], $args);
		}
	}

}

