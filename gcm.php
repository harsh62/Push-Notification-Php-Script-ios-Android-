<?php

    function gcm($devID,$message,$cert){
        echo "\n sending gcm to ". $devID ;
        
        $message = array("message" => "We\'re only a few minutes away from # kick-off. Time to tweet your support!",
                         "title" =>"Arsenal vs Fulham ");
        $registatoin_ids = array('APA91bHI-LugjHT-ZIgDnzAlnufR2flWmVVCpX9KcZiwJk1wropNMtlXfR5gQKyYi-VqdPzTfnDySRmf6A_SfG8WE4lPS8QKmtozcLqrerHkmrQlb7jkJH3xZZK482Y1lAfCLnRXoLev1d0Xr1lFVfys63n65MqAwg');
        $url = 'https://android.googleapis.com/gcm/send';
        
        $fields = array(
                        'registration_ids' => $registatoin_ids,
                        'data' => $message,
                        );
        
        $headers = array(
                         'Authorization: key='.$cert,
                         'Content-Type: application/json'
                         );
        // Open connection
        $ch = curl_init();
        
        
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        
        // Close connection
        curl_close($ch);
        echo $result;
    }
    $post_data = file_get_contents('php://input') ;
    
    gcm($countDeviceId2,$msg,'AIzaSyBT_NjP04tllpdlW1K1LC9qa9DwTKnZckg');

    
    
  
?>