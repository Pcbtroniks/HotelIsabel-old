<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Restaurant extends CI_Controller {

  public function index()
  {
    $this->twig->display('dining_room/dining_room');
  }
}
