<?php
namespace App\Object;

class DtoMessage
{
	public $listMessage=null;
	public $type=null;

	public function __construct()
	{
		$this->listMessage=[];
		$this->type='error';
	}

	public function existsMessage()
	{
		return count($this->listMessage)>0;
	}

	public function success()
	{
		$this->type='success';
	}

	public function warning()
	{
		$this->type='warning';
	}

	public function error()
	{
		$this->type='error';
	}
}
?>