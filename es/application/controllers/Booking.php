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

    private $responsable_name; // Nombre del responsable

    private $subject;

    # Room
    private $room;

    private $room_id; // External
    
    private $room_type; // Internal
    
    private $num_rooms; // Internal
    
    private $room_rack_price; // Internal

    # Payment
    
    private $payment_method; // External

    private $subtotal; // Internal

    private $total; // Internal

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
        # Set email data
        $this->folio = uniqid();

        # Set reservation data
        $this->nights = $this->input->post('nights');
        $this->room_id = $this->input->post('room_id');
        $this->num_rooms = $this->input->post('num_rooms') ?? 1;
        $this->check_in = $this->input->post('check_in');
        $this->check_out = $this->input->post('check_out');
        $this->adults = $this->input->post('adults');
        $this->kids = $this->input->post('kids');

        # Set responsable data
        $responsable_name = $this->input->post('name');
        $responsable_surname = $this->input->post('lastname');
        $responsable_email = $this->input->post('email');
        $responsable_phone = $this->input->post('phone');
        $observations_guest = $this->input->post('observations_guest'); 

        # Set payment data
        $payment_method = $this->input->post('payment_method');
        $cc_name = $this->input->post('cc_name');
        $cc_number = $this->input->post('cc_number');
        $cc_expires_mm = $this->input->post('cc_expires_mm');
        $cc_expires_yy = $this->input->post('cc_expires_yy');
        $cc_expires = $cc_expires_mm . '/' . $cc_expires_yy;
        $cc_cvv = $this->input->post('cc_cvv');


        # Get room
        $this->room = $this->room_model->get_room(intval($this->room_id));

        # Set room
        $this->room_type = $this->room['name'];
        $this->room_rack_price = $this->room['price'];

        # Set subtotal
        $this->subtotal = $this->set_subtotal($this->room_rack_price, $this->nights);
        
        # Set total
        $this->total = $this->set_total($this->subtotal, .19);

        # Save reservation
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
            'cc_expires' => $cc_expires,
            'cc_cvv' => $cc_cvv,
            'observations_guest' => $observations_guest,
            'subtotal' => $this->priceFormat($this->subtotal),
            'total' => $this->priceFormat($this->total),
        );

        $success = false;
        $sendEmail = false;
        if($this->book_now_model->register($reservation))
        {
            $success = true;
        }
        if($this->sendEmail($responsable_email))
        {
            $sendEmail = true;
        }

        $this->twig->display('booking/select_room', array(
            'reservation' => $reservation,
            'folio' => $this->folio,
            'sendEmail' => $sendEmail ? 'Exito': 'Error',
            'success' => $success ? 'Exito': 'Error',
        ));
    }

    public function success_booking()
    {
        $this->twig->display('booking/success_booking');
    }

    public function sendEmail($DestinataryEmail)
    {
        $this->load->library('email');
        $this->subject = '🛄 ¡Gracias! Tu reserva en Hotel Casino Plaza ha sido recibida.';

        # Set mail config
        $config['useragent'] = 'Rengine';
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'mail.hotelisabel.com';
        // $config['smtp_host'] = 'sandbox.smtp.mailtrap.io';
        $config['smtp_port'] = '465';
        // $config['smtp_port'] = '2525';
        $config['smtp_user'] = 'reservaciones.web@hotelisabel.com';
        // $config['smtp_user'] = '2363639b25db9c';
        // $config['smtp_pass'] = '2e7224a58cd3b9';
        $config['smtp_pass'] = 'j!q8T_E[b]*}ee#okM';
        $config['smtp_crypto'] = 'ssl';
        // $config['smtp_crypto'] = 'TLS';
        // $config['crlf'] = '\r\n';
        // $config['newline'] = '\r\n';
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = true;
        $config['priority'] = 1;

        // Initialize email library
        $this->email->initialize($config);

        $this->email->from('reservaciones.web@hotelisabel.com', 'Rengine');
        $this->email->to($DestinataryEmail);
        
        $this->email->subject($this->subject);
        $this->email->message($this->email_template());

        if($this->email->send())
        {
            return true;
        }
        else
        {
            return false;
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
                              <td bgcolor="#ffffff" width="100" align="left"><a href="https://www.hotelcasinoplaza.mx" target="_blank"><img alt="Logo" src="http://www.hotelcasinoplaza.mx/images/logo.png" width="50" height="78" style="display: block; font-family: Helvetica, Arial, sans-serif; color: #666666; font-size: 16px;" border="0"></a></td>
                              <td bgcolor="#ffffff" width="400" align="right" class="mobile-hide">
                                <table border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td align="right" style="padding: 0 0 5px 0; font-size: 14px; font-family: Arial, sans-serif; color: #666666; text-decoration: none;"><span style="color: #666666; text-decoration: none;">Hotel Casino Plaza<br>www.hotelcasinoplaza.mx</span></td>
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
                                            <a href="http://www.hotelcasinoplaza.mx" target="_blank"><img src="http://www.hotelcasinoplaza.mx/images/logo.png" width="200" border="0" alt="Logotipo" style="display: block; padding: 0; color: #666666; text-decoration: none; font-family: Helvetica, arial, sans-serif; font-size: 16px; width: 200px;" class="img-max"></a>
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
                          <span class="appleFooter" style="color:#666666;">Pedro Moreno 726
                          Centro Histórico
                          C.P. 44100
                          Guadalajara Jalisco, México.</span><br><a class="original-only" style="color: #666666; text-decoration: none;">Privacidad</a><span class="original-only" style="font-family: Arial, sans-serif; font-size: 12px; color: #444444;">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span><a style="color: #666666; text-decoration: none;">Este mail es de caracter confidencial</a>
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

    public function set_subtotal($roomPrice, $nights = 1)
    {
        return $roomPrice * $nights;
    }

    public function set_total($subtotal, $tax = .19)
    {
        return $subtotal + $tax;
    }

    private function priceFormat($price)
    {
      return number_format($price, 2, '.', ',');
    }
}