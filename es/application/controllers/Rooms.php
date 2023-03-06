<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rooms extends CI_Controller {

  public function index()
  {
    $this->twig->display('rooms/rooms');
  }
  
  public function habitacion_sencilla()
  {
      $this->twig->display('rooms/simple_room');
  }
  
  public function habitacion_doble()
  {
      $this->twig->display('rooms/double_room');
  }
  
  public function suite_luis_fernando()
  {
      $this->twig->display('rooms/suite_luis_fernando');
  }
  
  public function suite_isabel()
  {
      $this->twig->display('rooms/suite_isabel');
  }
  
  public function suite_paillaud()
  {
      $this->twig->display('rooms/suite_paillaud');
  }
  
  public function master_suite()
  {
      $this->twig->display('rooms/master_suite');
  }
  
  public function junior_suite()
  {
      $this->twig->display('rooms/junior_suite');
  }
}
