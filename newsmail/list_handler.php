<?php
define('BASE_PATH', rtrim(realpath(dirname(__FILE__)), "/") . '/');
require BASE_PATH . 'lib/mailing_list_handler_class.php';


validate_action(); // Perform validation on  $_GET['action']  before doing anything!



$mail_handler = new mail_handler(); // Create mail_handler object

 // ################## Perform Requested Action ##################

  switch ($_GET['action']) {
    case 'subscribe':
        subscribe_user(); // Attempt to add email to list!
    break;
    case 'unsubscribe':
        unsubscribe_user(); // Attempt to remove email from list!
    break;
    case 'show_list':
        $result = $mail_handler->show_subscribers(); // Returns a HTML list of all subscribers
        if ($result !== false) {
          respond($result, 'Content-Type: text/html; utf-8');
        } else {
          respond('Ingen email abonnenter endnu, eller der kunne ikke læses fra kilden.');
        }
    break;
    case 'subscribe_form':
    $html_part ='
  <form action="list_handler.php?action=subscribe" method="post" id="newsmail">
    <label for="name">Navn:</label>
    <input type="text" name="name" id="name" placeholder="John Doe">
    <label for="email">E-mail:</label>
    <input type="text" name="email" id="email" placeholder="john@example.com">
    <input type="submit" class="button" value="Tildmeld" class="ui_button">
  </form>';
    respond($html_part, 'Content-Type: text/html; utf-8');
    break;
    case 'unsubscribe_form':
    $html_part ='
  <form action="list_handler.php?action=unsubscribe" method="post" id="newsmail">
    <label for="email">E-mail:</label>
    <input type="text" name="email" id="email" placeholder="john@example.com">
    <input type="submit" class="button" value="Frameld" class="ui_button">
  </form>';
    respond($html_part, 'Content-Type: text/html; utf-8');
    break;
    case 'activate_email':
      respond('Din email er nu aktiveret.');
    break;
    default:
        respond('Ukendt handling!');
    break;
  }

 // ################## Functions ##################

function unsubscribe_user() {
  global $mail_handler;
  if(!empty($_POST['email'])) {
    $email = trim($_POST['email']); // Correction of rare typos causing whitespace at start and end of input

    validate_email($email); // Check if email is valid before we continiue (stops the script if not valid)
    
    $status = $mail_handler->unsubscribe_user($email);   
 
    if($status == 1) {
      respond('Email addressen blev fjernet fra listen.');
    } else if ($status == 2) {
      respond('Kunne ikke opdatere mailinglisten.');
    } else if ($status == 3) {
      respond('Addressen findes ikke på listen.');
    }

  }
}

function subscribe_user() {
  global $mail_handler;
  if((!empty($_POST['name'])) && (!empty($_POST['email']))) {
    $email = trim($_POST['email']); // Correction of rare typos causing whitespace at start and end of input

    validate_email($email); // Check if email is valid before we continiue (stops the script if not valid)

    if (preg_match('/^[\w ]{3,250}$/', $_POST['name'])) { // Validate the name field
      $name = trim($_POST['name']);
      $name = preg_replace('/\s+/', ' ', $name); // Replace exessive whitespace with a single space

      $status = $mail_handler->subscribe_user($email, $name); // Status = 1,2,3.. Etc.

      if ($status == 1) {
        respond('Du er nu blevet tilmeldt vores nyhedsmail.');
      } else if ($status == 2) {
        respond('Kunne ikke tilføje dig mailing listen.');
      } else if ($status == 3) {
        respond('E-mail addressen er allerede tilføjet, men den er ikke aktiveret.');
      } else if ($status == 4) {
        respond('E-mail addressen er allerede tilføjet.');
      }
    } else {respond('Ugyldigt navn');}
  }
}
function validate_email($email) {
  if (preg_match('/^[^@]{1,250}@[^@]{2,250}$/', $email) !== 1) { // Basic e-mail validation (Best simply try and send a message to the address)
    respond('En e-mail addresse skal have et "@" for at være gyldig, og må ikke være længere end 500 karaktere.');
  }
}
function validate_action() {
  $action_is_valid = false;
  // Validate the  $_GET['action']  parameter
  if(!isset($_GET['action'])) { // Make sure  $_GET['action']  is provided
    respond('Ukendt handling.');
  } else { // Make sure  $_GET['action']  is valid
    if(preg_match('/^[a-zA-Z_]{1,20}$/', $_GET['action']) !== 1) {
      respond('Ugyldig handling.');
    }
  }
}

function respond($source, $content_type='Content-Type: text/plain; utf-8') {
  header($content_type);
  echo $source;exit();
}
