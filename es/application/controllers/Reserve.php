<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reserve extends CI_Controller {

	public function index()
	{
		$this->twig->display('reserve/reserve');
	}
}
