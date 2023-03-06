<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking extends CI_Controller {

    private $check_in; // Fecha de entrada

    private $check_out; // Fecha de salida
    
    private $adults; // Número de adultos
    
    private $kids; // Número de niños
    
    private $num_guests; // Número total de huespedes
    
    private $nights; // Número de noches

    private $folio; // Folio de la reservación

    private $subject;

    # Room
    private $room;

    private $room_id; // External
    
    private $room_type; // Internal
    
    private $num_rooms; // Internal
    
    private $room_rack_price; // Internal

    public function __construct()
    {
        parent::__construct();
        $this->load->model('book_now_model');
        $this->load->model('room_model');
    }

    public function index()
    {
        $this->twig->display('booking/booking');
    }

    public function select_room()
    {
        $this->twig->display('booking/select_room');
    }

    public function book_now()
    {
        $this->getClientBookInfo();
        $this->room = $this->room_model->get_room(intval($this->input->get('room_id')));
        // echo '<pre>';
            // var_dump($this->room);
            // echo 'start_date ' . $this->check_in;
            // echo 'end_date ' .$this->check_out;
            // echo 'adults ' .$this->adults;
            // echo 'minors ' .$this->kids;
        // echo '</pre>';
        // die();
        $this->twig->display('booking/booking', array(
            'room' => $this->room,
            'booking' => $this->getClientBookInfo(),
        ));
    }

    public function getClientBookInfo()
    {
        $this->check_in = $this->input->get('start_date');
        $this->check_out = $this->input->get('end_date');
        $this->adults = intval($this->input->get('adults'));
        $this->kids = intval($this->input->get('minors'));

        // Calcular número de huespedes
        $this->num_guests = $this->adults + $this->kids;

        // Calcular número de noches
        $date_in = new DateTime($this->check_in);
        $date_out = new DateTime($this->check_out);
        $diff = $date_in->diff($date_out);
        $this->nights = ($diff->days < 1) ? 1 : $diff->days;

        return array(
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'adults' => $this->adults,
            'kids' => $this->kids,
            'num_guests' => $this->num_guests,
            'nights' => $this->nights,
        );
    }

    /**
     * Guardar la reservación
     */

    public function save_booking()
    {
        $post = $_POST;
        echo '<pre>';
            var_dump($post);
        echo '</pre>';
        die();

        # Set reservation data
        $this->room_id = $this->input->post('room_id');
        $this->num_rooms = $this->input->post('num_rooms');
        $this->check_in = $this->input->post('check_in');
        $this->check_out = $this->input->post('check_out');

        # Set responsable data

        $responsable_name = $this->input->post('name');
        $responsable_surname = $this->input->post('lastname');
        $responsable_email = $this->input->post('email');
        $responsable_phone = $this->input->post('phone');

        # Set payment data
        $payment_method = $this->input->post('payment_method');
        $cc_name = $this->input->post('cc_name');
        $cc_number = $this->input->post('cc_number');
        $cc_expires_mm = $this->input->post('cc_expires_mm');
        $cc_expires_yy = $this->input->post('cc_expires_yy');
        $cc_cvv = $this->input->post('cc_cvv');


        $this->room = $this->room_model->get_room(intval($this->input->get('room_id')));

        # Set room
        $this->room_type = $this->room['name'];
        $this->room_rack_price = $this->room['price'];

        $reservation = array(
            'folio' => $this->folio,
            'room_id' => $this->room_id,
            'num_rooms' => $this->num_rooms,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            're_name' => $responsable_name,
            're_surname' => $responsable_surname,
            're_email' => $responsable_email,
            're_phone' => $responsable_phone,
            'payment_method' => $payment_method,
            'cc_name' => $cc_name,
            'cc_number' => $cc_number,
            'cc_expires' => $cc_expires_mm . '/' . $cc_expires_yy,
            'cc_cvv' => $cc_cvv,
            'observations_guest' => $this->observations_guest,
            'subtotal' => $this->subtotal,
            'total' => $this->total
          );

            # Set email data
            $this->folio = uniqid();
            $this->subject = '🛄 ¡Gracias! Tu reserva en el Hotel Isabel';
    }

    public function set_subtotal($roomPrice, $nights)
    {
        return $roomPrice * $nights;
    }

    private function priceFormat($price)
    {
      return number_format($price, 2, '.', ',');
    }
}