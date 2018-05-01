<?php


class mail_handler {
  public $json_file;
  public $subscribers_arr = array();


  function __construct() {
    $this->json_file = BASE_PATH . 'email_subscribers.json';
  }

  public function subscribe_user($email, $name) {
   if($json_data = file_get_contents($this->json_file)) { // Check if we have a list of subscribers
     $this->subscribers_arr = json_decode($json_data, true);

     foreach($this->subscribers_arr as $key => $value) { // Check if e-mail is already in the list
       if ($key == $email) {
         if ($value["1"] == 0) {
           return 3; // E-mail already in list but NOT active
         } else {
           return 4; // E-mail already in list and active
         }
       }
     }
   }
   $this->subscribers_arr["$email"] = array($name, 0); // Add new e-mail to the list
   // If nothing else, finally try to write the subscribers file
   return $this->write_subscribers_to_file(); // Returns 1 or 2
  }

  public function unsubscribe_user($email) { // Removes the e-mail from the list
    if($json_file = file_get_contents($this->json_file)) {
      $this->subscribers_arr = json_decode($json_file, true);
      $return_value = 3;
      foreach($this->subscribers_arr as $key => $value) {
        if ($key !== $email) { // Save list without  $email
          $this->subscribers_arr["$email"] = $value;
        } else {
          $email_removed = true;
        }
      }
      if ($email_removed == true) {
        $return_value = $this->write_subscribers_to_file();
      }
      return $return_value;
    }
  }
  public function show_subscribers() {
   if($json_data = file_get_contents($this->json_file)) { // Check if we have a list of subscribers
     $this->subscribers_arr = json_decode($json_data, true);
     
     $html = '';
     foreach($this->subscribers_arr as $key => $value) {

      $html .= '<tr> <td>'.trim($value["0"], '"').'</td> <td>'.$key.'</td> <td>'.$value["1"].'</td><td>';
     }
     $html = '<tr> <th>Name</th> <th>Email</th> <th>Active?</th> <th>' . $html;
     $html = '<table>' . $html . '</table>';
     
     return $html;
   } else {return false;} // No subscribers found / unable to read file
  }

  private function write_subscribers_to_file() {
    // Attempt to write subscribers list to file
    if(file_put_contents($this->json_file, json_encode($this->subscribers_arr), LOCK_EX) !== false) {
      return 1; // User successfully added or removed to/from subscribers file
    } else {
      return 2; // Could not write to the subscribers file (Usually a problem with file-permissions)
    }
  }


}
