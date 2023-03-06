<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Room_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database('default');
  }

  public function get_room($room_id)
  {
    $query = $this
        ->db
        ->from('rooms')
        ->where('room_id', $room_id)
        ->get();
    return $query->row_array();
  }

}
