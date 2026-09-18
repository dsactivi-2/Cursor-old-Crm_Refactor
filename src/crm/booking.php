<?php

/*
$myXMLData =
'
<!--?xml version="1.0" encoding="UTF-8"?-->
<request>
  <username>Jobstep-elvis</username>
  <password>_@K*m=mwyY)sZ/wj?OMi(DEonPvTUS/5(MFA3tpw</password>
</request>
';

$xml=simplexml_load_string($myXMLData) or die("Error: Cannot create object");
print_r($xml);
*/

/*
  $url = 'https://secure-supply-xml.booking.com/hotels/xml/reservations';
  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt( $ch, CURLOPT_POST, true );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_POSTFIELDS, 
  '
<!--?xml version="1.0" encoding="UTF-8"?-->
<request>
  <username>Jobstep-elvis</username>
  <password>_@K*m=mwyY)sZ/wj?OMi(DEonPvTUS/5(MFA3tpw</password>
</request>
  '
  );
  $result = curl_exec($ch);
  $info = curl_getinfo($ch);
  
	if (curl_exec($ch) === FALSE) {
	die("Curl Failed: " . curl_error($ch));
	} else {
	return curl_exec($ch);
	}

  curl_close($ch);
  */
  
// $xml_str = '<!--?xml version="1.0" encoding="UTF-8"?-->
// <request>
  // <username>Jobstep-elvis</username>
  // <password>_@K*m=mwyY)sZ/wj?OMi(DEonPvTUS/5(MFA3tpw</password>
// </request>'
// $url = 'https://secure-supply-xml.booking.com/hotels/xml/reservations'

// $post_data = array('xml' => $xml_str);
// $stream_options = array(
    // 'http' => array(
        // 'method'  => 'POST',
        // 'header'  => 'Content-type: application/x-www-form-urlencoded' . "\r\n",
        // 'content' =>  http_build_query($post_data)));

// $context  = stream_context_create($stream_options);
// $response = file_get_contents($url, null, $context); 
?>
