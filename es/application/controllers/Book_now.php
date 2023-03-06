<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Book_now extends CI_Controller {

  # Email data
  private $folio; // Internal
  private $subject; // Internal

  # Responsable
  private $responsable_name; // External
  private $responsable_surname; // External
  private $responsable_email; // External
  private $responsable_phone; // External
  private $observations_guest; // External

  # Room
  private $room;
  private $room_id; // External
  private $room_type; // Internal
  private $num_rooms; // Internal
  private $room_rack_price; // Internal

  # Reservation
  private $check_in; // External
  private $check_out; // External
  private $additional_guests; // Internal
  private $adults; // External
  private $kids; // External
  private $subtotal; // Internal
  private $total; // Internal
  private $offer_percentage; // Internal
  private $nights; // Internal

  # Payment
  private $payment_method; // External
  private $cc_name; // External
  private $cc_number; // External
  private $cc_expires_mm; // External
  private $cc_expires_yy; // External
  private $cc_cvv; // External

  public function __construct()
  {
    parent::__construct();
    $this->load->model('room_model');
    $this->load->model('book_now_model');

    if( !empty($this->input->get('room_id')) )
    {
      $this->room_id = $this->input->get('room_id');
      $this->check_in = $this->input->get('check_in');
      $this->check_out = $this->input->get('check_out');
      $this->adults = $this->input->get('adults');
      $this->kids = $this->input->get('kids');
    }
    else if( !empty($this->input->post('room_id')) )
    {
      $this->room_id = $this->input->post('room_id');
      $this->check_in = $this->input->post('check_in');
      $this->check_out = $this->input->post('check_out');
      $this->adults = $this->input->post('adults');
      $this->kids = $this->input->post('kids');
    }

    # Validar campos
    $this->validate_values();

    # Set room
    $this->room = $this->room_model->get_room(intval($this->room_id));
    $this->room_type = $this->room['name'];
    $this->room_rack_price = $this->room['price'];
    
    # Set reservation
    $this->set_num_rooms($this->room);
    $this->set_additional_guests($this->room);
    $this->set_nights($this->check_in, $this->check_out);
    $this->set_offer();
    $this->set_subtotal();
    $this->set_total();
  }

  public function index()
  {
    $this->twig->display('reserve/reserve', array('room' => array(
      'room_id' => $this->room['room_id'],
      'name' => $this->room['name'],
      'description' => $this->room['description'],
      'num_rooms' => $this->num_rooms,
      'check_in' => $this->check_in,
      'check_out' => $this->check_out,
      'adults' => $this->adults,
      'kids' => $this->kids,
      'subtotal' => $this->price_format($this->subtotal),
      'taxes' => $this->price_format($this->subtotal * (0.19))
    )));
  }

  private function price_format($price)
  {
    return number_format($price, 2, '.', ',');
  }

  private function set_num_rooms()
  {
    $this->num_rooms = ceil(($this->adults + $this->kids) / $this->room['max_capacity']);
  }

  private function set_additional_guests()
  {
    $guests_inc = ($this->num_rooms * $this->room['default_capacity']);
    $this->additional_guests = (($this->adults + $this->kids) - $guests_inc);
  }

  private function set_nights($check_in, $check_out)
  {
    $date_in = new DateTime($check_in);
    $date_out = new DateTime($check_out);
    $diff = $date_in->diff($date_out);
    $this->nights = ($diff->days < 1) ? 1 : $diff->days;
  }

  private function set_subtotal()
  {
    $guests_rate = ($this->room['additional_guest'] * $this->additional_guests * $this->nights);
    $guests_rate = ($guests_rate > 0) ? $guests_rate : 0;

    $this->subtotal = (
      ($this->room_rack_price * $this->nights * $this->num_rooms) + $guests_rate
    );

    if($this->offer_percentage > 0)
    {
      for($i = 0; $i < $this->num_rooms; $i++)
      {
        $this->subtotal -= $this->subtotal * ($this->offer_percentage / 100);
      }
    }
  }

  public function set_offer()
  {
    $offers = $this->book_now_model->get_offers($this->room_id);
    $check_in = $this->date_clean($this->check_in);
    $check_out = $this->date_clean($this->check_in);

    $offer_percentage = $offers[0]['percentage'] ?? 0;

    if(is_array($offers))
    {
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
    }

    $this->offer_percentage =  $offer_percentage;
  }

  public function date_clean(string $date)
  {
    $date = date_create($date);
    return date_format($date, 'Ymd');
  }

  private function set_total()
  {
    # 16% IVA, 3% Impuesto Municipal
    $taxes = $this->subtotal * (0.19);

    $this->total = $this->subtotal + $taxes;
  }

  public function send()
  {
    $this->load->library('email');

    # Set email data
    $this->folio = uniqid();
    $this->subject = '🛄 ¡Gracias! Tu reserva en el Hotel Isabel';

    # Set room
    $this->room_type = $this->room['name'];
    $this->room_rack_price = $this->room['price'];

    # Set responsable
    $this->responsable_name = $this->input->post('responsable_name');
    $this->responsable_surname = $this->input->post('responsable_surname');
    $this->responsable_email = $this->input->post('responsable_email');
    $this->responsable_phone = $this->input->post('responsable_phone');
    $this->observations_guest = $this->input->post('observations_guest');

    # Set payment
    $this->payment_method = $this->input->post('payment_method');
    $this->cc_name = $this->input->post('cc_name');
    $this->cc_number = $this->input->post('cc_number');
    $this->cc_expires_mm = $this->input->post('cc_expires_mm');
    $this->cc_expires_yy = $this->input->post('cc_expires_yy');
    $this->cc_cvv = $this->input->post('cc_cvv');

    # Set mail config
    $config['useragent'] = 'Rengine';
    $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'mail.hotelisabel.com';
    $config['smtp_user'] = 'reservaciones.web@hotelisabel.com';
    $config['smtp_pass'] = 'j!q8T_E[b]*}ee#okM';
    $config['smtp_port'] = '465';
    $config['smtp_crypto'] = 'ssl';
    $config['charset'] = 'utf-8';
    $config['mailtype'] = 'html';
    $config['wordwrap'] = true;
    $config['priority'] = 1;
    
    $this->email->initialize($config);

    $this->email->from('reservaciones.web@hotelisabel.com', 'Hotel Isabel');
    $this->email->to($this->responsable_email);
    
    $this->email->subject($this->subject);
    $this->email->message($this->email_template());

    $reservation = array(
      'folio' => $this->folio,
      'room_id' => $this->room_id,
      'num_rooms' => $this->num_rooms,
      'check_in' => $this->check_in,
      'check_out' => $this->check_out,
      're_name' => $this->responsable_name,
      're_surname' => $this->responsable_surname,
      're_email' => $this->responsable_email,
      're_phone' => $this->responsable_phone,
      'payment_method' => $this->payment_method,
      'cc_name' => $this->cc_name,
      'cc_number' => $this->cc_number,
      'cc_expires' => $this->cc_expires_mm . '/' . $this->cc_expires_yy,
      'cc_cvv' => $this->cc_cvv,
      'observations_guest' => $this->observations_guest,
      'subtotal' => $this->subtotal,
      'total' => $this->total
    );

    if($this->email->send() && $this->book_now_model->register($reservation))
    {
      $this->twig->display('reserve/successful', array('reservation' => array(
        'folio' => $this->folio
      )));
    }
    else
    {
      echo 'false';
    }


  }

  public function successful()
  {
    $this->twig->display('reserve/successful');
  }

  private function validate_values()
  {
    if
    (
      empty($this->room_id) ||
      empty($this->check_in) ||
      empty($this->check_out) ||
      empty($this->adults)
    )
    {
      redirect(base_url());
    }
  }

  public function email_template()
  {
    return
    '
    <html>
    <head>
      <title>'. $this->subject .'</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width">
      <style type="text/css">
      /* CLIENT-SPECIFIC STYLES */
      #outlook a{padding:0;} /* Force Outlook to provide a "view in browser" message */
      .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
      .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing */
      body, table, td, a{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
      table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up */
      img{-ms-interpolation-mode:bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */
  
      /* RESET STYLES */
      body{margin:0; padding:0;}
      img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
      table{border-collapse:collapse !important;}
      body{height:100% !important; margin:0; padding:0; width:100% !important;}
  
      /* iOS BLUE LINKS */
      .appleBody a {color:#68440a; text-decoration: none;}
      .appleFooter a {color:#999999; text-decoration: none;}
  
      /* MOBILE STYLES */
      @media screen and (max-width: 525px) {
  
        /* ALLOWS FOR FLUID TABLES */
        table[class="wrapper"]{
          width:100% !important;
        }
  
        /* ADJUSTS LAYOUT OF LOGO IMAGE */
        td[class="logo"]{
          text-align: left;
          padding: 20px 0 20px 0 !important;
        }
  
        td[class="logo"] img{
          margin:0 auto!important;
        }
  
        /* USE THESE CLASSES TO HIDE CONTENT ON MOBILE */
        td[class="mobile-hide"]{
          display:none;}
  
          img[class="mobile-hide"]{
            display: none !important;
          }
  
          img[class="img-max"]{
            max-width: 100% !important;
            height:auto !important;
          }
  
          /* FULL-WIDTH TABLES */
          table[class="responsive-table"]{
            width:100%!important;
          }
  
          /* UTILITY CLASSES FOR ADJUSTING PADDING ON MOBILE */
          td[class="padding"]{
            padding: 10px 5% 15px 5% !important;
          }
  
          td[class="padding-copy"]{
            padding: 10px 5% 10px 5% !important;
            text-align: center;
          }
  
          td[class="padding-meta"]{
            padding: 30px 5% 0px 5% !important;
            text-align: center;
          }
  
          td[class="no-pad"]{
            padding: 0 0 20px 0 !important;
          }
  
          td[class="no-padding"]{
            padding: 0 !important;
          }
  
          td[class="section-padding"]{
            padding: 50px 15px 50px 15px !important;
          }
  
          td[class="section-padding-bottom-image"]{
            padding: 50px 15px 0 15px !important;
          }
  
          /* ADJUST BUTTONS ON MOBILE */
          td[class="mobile-wrapper"]{
            padding: 10px 5% 15px 5% !important;
          }
  
          table[class="mobile-button-container"]{
            margin:0 auto;
            width:100% !important;
          }
  
          a[class="mobile-button"]{
            width:80% !important;
            padding: 15px !important;
            border: 0 !important;
            font-size: 16px !important;
          }
  
        }
        </style>
      </head>
      <body style="margin: 0; padding: 0;">
  
        <!-- HEADER -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
          <tr>
            <td bgcolor="#ffffff">
              <div align="center" style="padding: 0px 15px 0px 15px;">
                <table border="0" cellpadding="0" cellspacing="0" width="500" class="wrapper">
                  <!-- LOGO/PREHEADER TEXT -->
                  <tr>
                    <td style="padding: 20px 0px 30px 0px;" class="logo">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <td bgcolor="#ffffff" width="100" align="left"><a href="https://www.hotelisabel.com/es/" target="_blank"><img alt="Logo" src="https://www.hotelisabel.com/es/img/logoemail.png" width="50" height="78" style="display: block; font-family: Helvetica, Arial, sans-serif; color: #666666; font-size: 16px;" border="0"></a></td>
                          <td bgcolor="#ffffff" width="400" align="right" class="mobile-hide">
                            <table border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                <td align="right" style="padding: 0 0 5px 0; font-size: 14px; font-family: Arial, sans-serif; color: #666666; text-decoration: none;"><span style="color: #666666; text-decoration: none;">Hotel Isabel<br>www.hotelisabel.com</span></td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
            </td>
          </tr>
        </table>
  
        <!-- ONE COLUMN SECTION -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
          <tr>
            <td bgcolor="#ffffff" align="center" style="padding: 70px 15px 70px 15px;" class="section-padding">
              <table border="0" cellpadding="0" cellspacing="0" width="500" class="responsive-table">
                <tr>
                  <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>
                          <!-- HERO IMAGE -->
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                              <tr>
                                <td class="padding-copy">
                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td align="center">
                                        <a href="https://www.hotelisabel.com/es/" target="_blank"><img src="https://www.hotelisabel.com/es/img/logoemail.png" width="200" border="0" alt="Logotipo" style="display: block; padding: 0; color: #666666; text-decoration: none; font-family: Helvetica, arial, sans-serif; font-size: 16px; width: 200px;" class="img-max"></a>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <!-- COPY -->
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td align="center" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">¡Gracias, '.$this->responsable_name.'!</td>
                            </tr>
                            <tr>
                              <td align="center" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Hemos recibido tu reservación.</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
  
                      <tr>
                        <td>
                          <!-- BULLETPROOF BUTTON -->
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
  
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;">Folio:</th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;">'.$this->folio.'</td>
                            </tr>
  
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;">Tu reserva:</th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;">
                              '. $this->nights .' Noche(s), '. $this->num_rooms . ' Habitacione(s)</td>
                            </tr>
  
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;">Reservaste para:</th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;">'.$this->adults.' Adulto(s), y '. $this->kids.' Niño(s)</td>
                            </tr>
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;">Entrada:</th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;">'.$this->check_in.'</td>
                            </tr>
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;">Salida:</th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;">'.$this->check_out.'</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
  
  
  
                      <tr>
                        <td>
  
                          <!-- BULLETPROOF BUTTON -->
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
  
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;"></th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;"></td>
                            </tr>
  
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;">'.$this->num_rooms. ' x '. $this->room_type .'</th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;"></td>
                            </tr>
  
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;">16% IVA y 3% de impuesto municipal incluido.</th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;"></td>
                            </tr>
  
                            <tr style="height: 32px;">
                              <th align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;">Precio total:</th>
                              <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #000000;">MXN '.$this->total.'</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  
  
  
  
    <!-- FOOTER -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
      <tr>
        <td bgcolor="#ffffff" align="center">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
              <td style="padding: 20px 0px 20px 0px;">
                <!-- UNSUBSCRIBE COPY -->
                <table width="500" border="0" cellspacing="0" cellpadding="0" align="center" class="responsive-table">
                  <tr>
                    <td align="center" valign="middle" style="font-size: 12px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;">
                      <span class="appleFooter" style="color:#666666;">José Guadalupe Montenegro 1572, Americana, 44160 Guadalajara, Jal.</span><br><a class="original-only" style="color: #666666; text-decoration: none;">Privacidad</a><span class="original-only" style="font-family: Arial, sans-serif; font-size: 12px; color: #444444;">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span><a style="color: #666666; text-decoration: none;">Este mail es de caracter confidencial</a>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  
    </body>
    </html>
    ';
  }
}
