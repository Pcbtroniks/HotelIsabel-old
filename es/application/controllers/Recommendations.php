<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recommendations extends CI_Controller {

  private $check_in; // Fecha de entrada
  private $check_out; // Fecha de salida
  private $adults; // Número de adultos
  private $kids; // Número de niños
  private $num_guests; // Número total de huespedes
  private $nights; // Número de noches

  public function __construct()
  {
    parent::__construct();
    $this->load->model('recommendations_model');
    $this->load->model('book_now_model');
  }

  public function rooms()
  {
    $this->set_config();
    $this->validate_values();

    $recommend_rooms = $this->recommend_rooms();
    
    //var_dump($recommend_rooms);
    //exit;

    $this->twig->display('rengine/recomendations', array(
      'recommend_rooms' => $recommend_rooms
    ));

    /**
     * Habitaciones
     *
     * Estándar, capacidad: 4 personas.
     * Superior sencilla, capacidad: 2 personas.
     * Superior doble, capacidad: 4 personas.
     */
  }

  private function validate_values()
  {
    if
    (
      empty($this->check_in) ||
      empty($this->check_out) ||
      empty($this->adults) ||
      empty($this->nights)
    ):
      redirect(base_url());
    endif;
  }  

  private function set_config()
  {
    $this->check_in = $this->input->get('check_in');
    $this->check_out = $this->input->get('check_out');
    $this->adults = intval($this->input->get('adults'));
    $this->kids = intval($this->input->get('kids'));

    // Calcular número de huespedes
    $this->num_guests = $this->adults + $this->kids;

    // Calcular número de noches
    $date_in = new DateTime($this->check_in);
    $date_out = new DateTime($this->check_out);
    $diff = $date_in->diff($date_out);
    $this->nights = ($diff->days < 1) ? 1 : $diff->days;
  }

  private function recommend_rooms()
  {
    $rooms = $this->recommendations_model->get_rooms();
    $recommendations = array();

    foreach ($rooms as $room)
    {
      // Habitaciones necesarias
      $num_rooms = ceil($this->num_guests / $room['max_capacity']);

      // Calcular huespedes incluidos
      $guests_inc = ($num_rooms * $room['default_capacity']);

      // Calcular huespedes extra
      $guests_extra = ($this->num_guests - $guests_inc);

      // Calcular costo de huespedes extra por noche
      $guests_rate = (($room['additional_guest'] * $guests_extra) * $this->nights);
      $guests_rate = ($guests_rate > 0) ? $guests_rate : 0;

      // Calcular subtotal
      $subtotal = (($room['price'] * $num_rooms * $this->nights) + $guests_rate);

      //Aplicar descuento
      $offer_percentage = $this->get_offer($room['room_id']) ?? 0;

      if($offer_percentage > 0)
      {
        for($i = 0; $i < $num_rooms; $i++)
        {
          $subtotal -= $subtotal * ($offer_percentage / 100);
        }
      }

      // Calcular impuestos
      $iva = 0.16;
      $impuesto_municipal = 0.03;
      $taxes = $iva + $impuesto_municipal;
      $taxes = ($subtotal * $taxes);

      // ¿Cuento con suficientes habitaciones?
      if($num_rooms <= $room['availability'])
      {
        $recommendation = array(
          'room_id' => $room['room_id'],
          'name' => $room['name'],
          'description' => $room['description'],
          'image_cover' => $room['image_cover'],
          'nights' => $this->nights,
          'rooms' => $num_rooms,
          'check_in' => $this->check_in,
          'check_out' => $this->check_out,
          'adults' => $this->adults,
          'kids' => $this->kids,
          'price_night' => $room['price'],
          'subtotal' => $this->price_format($subtotal),
          'taxes' => $this->price_format($taxes)
        );
        // Añadir tipo de habitación a recomendacion
        array_push($recommendations, $recommendation);
      }
    }

    return $recommendations;
  }

  private function price_format($price)
  {
    return number_format($price, 2, '.', ',');
  }

    public function get_offer($room_id)
  {
    $offers = $this->book_now_model->get_offers($room_id);
    $check_in = $this->date_clean($this->check_in);
    $check_out = $this->date_clean($this->check_in);

    $offer_percentage = $offers[0]['percentage'] ?? 0;

    if(!is_array($offers))
    {
      return $offer_percentage;
    }

    for($i = 0; $i < count($offers); $i++)
    {
      $start_date = $this->date_clean($offers[$i]['start_date']);
      $end_date = $this->date_clean($offers[$i]['end_date']);

      if($check_in >= $start_date && $check_out <= $end_date)
      {
        if($offers[$i]['percentage'] > $offer_percentage)
        {
          $offer_percentage = $offers[$i]['percentage'];
        }
      }
    }

    return $offer_percentage;
  }

  public function date_clean(string $date)
  {
    $date = date_create($date);
    return date_format($date, 'Ymd');
  }
}
