<?php
    
    function apns($message,$devID){
        //echo "\n in apns ". $devID."   ".  $message;
		echo "\n in apns ". $devID;
        
        $passphrase = 'guinnessplus';
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', '/usr/local/apache2/htdocs/push/guinness_push_cert_PROD.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client(
                                   'ssl://gateway.push.apple.com:2195', $err,
                                   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        echo "\n Connected to APNS" . PHP_EOL;
        // Create the payload body
        $body['aps'] = array(
                             'alert' => $message,
                             'sound' => 'default'
                             );
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*',$devID) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        
        if (!$result)
            echo "\n Message not delivered" . PHP_EOL;
        else
            echo "\n Message successfully delivered" . PHP_EOL;
        
        
        // Close the connection to the server
        fclose($fp);
    }

    function gcm($androidDeviceIds,$pushNotificationMessage,$certificationIdForAndroid){

        $url = 'https://android.googleapis.com/gcm/send';
        
        $fields = array(
                        'registration_ids' => $androidDeviceIds,
                        'data' => $pushNotificationMessage,
                        );
        
        $headers = array(
                         'Authorization: key='.$certificationIdForAndroid,
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
            return array('failure' =>'Curl failed: ' . curl_error($ch));
        }
        
        // Close connection
        curl_close($ch);
        return array('success'=>$result);
    }
    
    //Post data Recieved
    $postDataJson = file_get_contents('php://input') ;
    //Decoded post json into Php Array
    $decodedJsonArray=json_decode($postDataJson,true);
    
    //Get Certification Details for Android
    $certificationIdForAndroid = $decodedJsonArray['certificationDetails']['orIfDeviceTypeAndroid']['certificationId'];
    
    //Get PEM file Details for iOS
    $locationOfPemFile = $decodedJsonArray['certificationDetails']['ifDeviceTypeIos']['locationOfPemFile'];
    $passPhraseOfPemFile = $decodedJsonArray['certificationDetails']['ifDeviceTypeIos']['passPhrase'];
    
    //Get Message To be posted from JSON posted
    
    $pushNotificationMessage = $decodedJsonArray['messageDetails'];
    
    //FetchDeviceId's For Android
    $androidDeviceIds = $decodedJsonArray['deviceDetails']['android'];
    
    //FetchDeviceId's For iOS
    $iosDeviceIds = $decodedJsonArray['deviceDetails']['ios'];
    
    //Call GCM Push NOtification function if the device count is grater than zero
    $androidPushStatus = array();
    if(count($androidDeviceIds)>0){
       $androidPushStatus = gcm($androidDeviceIds,$pushNotificationMessage,$certificationIdForAndroid);
    }
    
    //Call APNS Push NOtification function if the device count is grater than zero
    if(count($iosDeviceIds)>0){
        apns($msg,$countDeviceId2);
    }
    
    header(json_encode($androidPushStatus,true));    
?>