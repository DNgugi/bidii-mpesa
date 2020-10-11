<?php 

function bidii_mpesa_generate_token(){
        $consumer_key = "6fSidiQK1v1f9sJG9m8Tzbs3SVTPgYfW";
        $consumer_secret = "3hosoK1vkgnXv80u";
      
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);

         $token = json_decode($curl_response)->body->access_token;
         return $token;
    }