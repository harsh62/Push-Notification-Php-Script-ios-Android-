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
  
?>