<?php

$data ='{
  "user": {
    "email": "office@cashbackholic.ro",
    "password": "1Digit@lMint1220"
  }
}';
                                                                                                                    
$ch = curl_init('https://api.2performant.com/users/sign_in.json');                                                                      
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($data))                                                                       
);                                                                                                                   
   $result = curl_exec($ch);                                                                                                                  
var_dump($result);
echo "<br><br><hr><br><br>";
curl_setopt($ch, CURLOPT_HEADER, 1);
$c = curl_exec($ch);
echo $c;
curl_close($ch);

?>