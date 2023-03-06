<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Book_now_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database('default');
  }

  public function register(array $reservation)
  {
    $query = $this->db->insert('reservations', $reservation);
    return ($query === true) ? true : false;
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

  public function get_offers($room_id)
  {
    $query = $this
      ->db
      ->select('d.discount_code, d.percentage, d.start_date, d.end_date, rd.room_id', false)
      ->from('discounts as d', false)
      ->join('rooms_discounts as rd', 'd.discount_id = rd.discount_id', false)
      ->where('rd.room_id', $room_id)
      ->get();

    if($query->num_rows() > 0)
    {
      return $query->result_array();
    }

    return false;
  }

}