<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recommendations_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database('default');
  }

  public function get_rooms()
  {
    $query = $this
        ->db
        ->from('rooms')
        ->where('availability > 1')
        ->get();
    return $query->result_array();
  }

  public function list_customers()
  {
    $query = $this
        ->db
        ->select('cu.customer_id, cu.customer, u.email, cu.status, cu.created_at, cu.updated_at', false)
        ->from('customers as cu', false)
        ->join('users as u', 'cu.user_id = u.user_id', 'left')
        ->order_by('cu.updated_at ASC', false)
        ->get();
    return $query->result_array();
  }

  public function get_customer($customer_id = null)
  {
    $query = $this
    ->db
    ->select('cu.customer_id, cu.customer, u.user_id, u.email, cu.status, cu.created_at, cu.updated_at', false)
    ->from('customers as cu', false)
    ->join('users as u', 'cu.user_id = u.user_id', 'left')
    ->where('customer_id', $customer_id)
    ->order_by('cu.updated_at ASC', false)
    ->get();

    return $query->row_array();
  }

  public function create_customer()
  {
    $data = [
      'customer_id' => 0,
      'customer' => $this->input->post('customer'),
      'user_id' => $this->input->post('user_id')
    ];

    $sql = $this->db->insert('customers', $data);

    return ($sql === true) ? true : false;
  }

  public function edit_customer($customer_id = null)
  {
    if($customer_id != null)
    {
        $this->db->trans_start();

        $customer_data = [
          'customer' => $this->input->post('customer'),
          'user_id' => $this->input->post('user_id'),
          'status' => $this->input->post('status')
        ];

      $this
      ->db
      ->where('customer_id', $customer_id)
      ->update('customers', $customer_data);

      $this->db->trans_complete();
      return $this->db->trans_status();
    }
  }

  public function customer_owner($customer_id = '', $customer = '')
  {
    if(!empty($customer_id) && !empty($customer))
    {
      if(!$this->exists_on_db($customer, 'customer', 'customers')) // El campo no existe en la base de datos.
      {
        return TRUE;
      }
      else // Comprobar si el valor que existe pertenece a la fila que intentamos editar.
      {
        $data = ['customer_id' => $customer_id, 'customer' => $customer];
        $this->db->from('customers')->where($data);

        $num_records = $this->db->count_all_results();
        return ($num_records == 1) ? TRUE : FALSE;
      }
    }

    return FALSE; // No se recibieron los parametros de la función.
  }

  public function customer_email_owner($customer_id = '', $customer_email = '')
  {
    if(!empty($customer_id) && !empty($customer_email))
    {
      if(!$this->exists_on_db($customer_email, 'customer_email', 'customers')) // El campo no existe en la base de datos.
      {
        return TRUE;
      }
      else // Comprobar si el valor que existe pertenece a la fila que intentamos editar.
      {
        $data = ['customer_id' => $customer_id, 'customer_email' => $customer_email];
        $this->db->from('customers')->where($data);

        $num_records = $this->db->count_all_results();
        return ($num_records == 1) ? TRUE : FALSE;
      }
    }

    return FALSE; // No se recibieron los parametros de la función.
  }

  /*========================================
  =            HELPER FUNCTIONS            =
  ========================================*/

  public function exists_on_db($value = null, $field = null, $table = null)
  {
    return $this->advanced_queries->exists_on_db($value, $field, $table);
  }

  public function select2($id = null, $text = null, $table = null, $searchTerm = null)
  {
    return $this->advanced_queries->select2($id, $text, $table, $searchTerm);
  }

}
