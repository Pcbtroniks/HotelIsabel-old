<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Packages extends CI_Controller {

  public function index()
  {
    $this->twig->display('packages/packages');
  }
}
