<?php
use Plutonex\Flow\Port;


class ObserverTest1 extends \Plutonex\Watch\Observer
{

	public function onConnect($subject)
	{
		$subject->log('OnConnect action done from ' . __CLASS__ . ' by socket on port '. $subject->getPortName());
	}

	public function onReady($subject)
	{
		$data = $subject->getData();
		$data = $data . ' 1 ...';
		$subject->log('data modified by '. __CLASS__);
		$subject->setData($data);

		//$subject->log(' @@@@@@@@ DISCONNECT PORT BY OBS 1 @@@@@@@@@');
		//$subject->disconnect();
	}

	// public function onDisconnect($subject)
	// {
	// 	$data = $subject->getData();
	// 	$subject->log('disconnecting by port '.$subject->getPortName());
	// 	$data = $data  . ' stopping ' . $subject->getPortName();
	// 	$subject->setData($data);
	// }

}

class ObserverTest2 extends \Plutonex\Watch\Observer
{
	public function onConnect($subject)
	{
		$subject->log('OnConnect action done from ' . __CLASS__ . ' by socket on port '. $subject->getPortName());
	}

	public function onReady($subject)
	{
		$data = $subject->getData();
		$data = $data . ' 2 ...';
		$subject->log('@@@ data modified by '. __CLASS__);
		$subject->setData($data);

	}

	// public function onDisconnect($subject)
	// {
	// 	$data = $subject->getData();
	// 	$subject->log('disconnecting by port '.$subject->getPortName());
	// 	$data = $data  . ' stopping ' . $subject->getPortName();
	// 	$subject->setData($data);
	// }

}

class ObserverTest3 extends \Plutonex\Watch\Observer
{
	public function onConnect($subject)
	{
		$subject->log('OnConnect action done from ' . __CLASS__ . ' by socket on port '. $subject->getPortName());
	}

	public function onReady($subject)
	{
		$data = $subject->getData();
		$data = $data . ' 3 ...';
		$subject->log('data modified by '. __CLASS__);
		$subject->setData($data);
		$subject->terminate();
	}

	// public function onDisconnect($subject)
	// {
	// 	$data = $subject->getData();
	// 	$subject->log('disconnecting by port '.$subject->getPortName());
	// 	$data = $data  . ' stopping ' . $subject->getPortName();
	// 	$subject->setData($data);
	// }

}


class ObserverTest4 extends \Plutonex\Watch\Observer
{
	public function onConnect($subject)
	{
		$subject->log('OnConnect action done from ' . __CLASS__ . ' by socket on port '. $subject->getPortName());
	}

	public function onReady($subject)
	{
		
		$data = $subject->getData();
		$data = $data . ' 4 ...' . PHP_EOL;
		$subject->log('data modified by '. __CLASS__);
		$subject->setData($data);
	}

	// public function onDisconnect($subject)
	// {
	// 	$data = $subject->getData();
	// 	$subject->log('disconnecting by port '.$subject->getPortName());
	// 	$data = $data  . ' stopping ' . $subject->getPortName();
	// 	$subject->setData($data);
	// }

}

class ProcessTest extends PHPUnit_Framework_TestCase
{

	// public function setUp()
	// {

	// }


	public function testPortInstance()
	{
		$port = new \Plutonex\Flow\Port('test');

		$this->assertTrue(true);
	}


	public function testSocketConnect()
	{

		$port_A = new \Plutonex\Flow\Port('A');
		$port_B = new \Plutonex\Flow\Port('B');
		$port_C = new \Plutonex\Flow\Port('C');

		$socketA = new \Plutonex\Flow\Socket();
		$socketB = new \Plutonex\Flow\Socket();
		$socketC = new \Plutonex\Flow\Socket();

		//bind observers to sockets
		$socketA->attach(new ObserverTest1);
		$socketA->attach(new ObserverTest2);
		$socketB->attach(new ObserverTest3);
		$socketC->attach(new ObserverTest4);

		
		//bind sockets to ports
		$port_A->attach($socketA);
		$port_B->attach($socketB);
		$port_C->attach($socketC);


		//network between ports
		// $port_C->connectTo($port_A);
		$port_B->connectTo($port_C);
		$port_A->connectTo($port_B);
		
	
		//run a port
		$port_A->connect();
		$port_A->input('start ...'); //insert data to the first port
		//$port_A->disconnect();

		//echo "auth port sent " . $socketA->numObserversSent('onReady') . " observers";

		// $port_B->connect();
		// $port_B->input($port_A->output());

		echo $port_A->output();
		//echo $port_B->output();

		echo PHP_EOL . "LOGS :". PHP_EOL;
		echo $port_A->getLogs(true);
	}

}