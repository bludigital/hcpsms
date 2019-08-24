<?php
$birthdate = date("Y-m-d", strtotime($_POST['start_date']));
$birthdate_time = $birthdate . "T00:00:00.000Z";
$opt_in_status = 'false';
$mobile_refresh = $_POST['telclear'];
$mobile_refresh = base64_encode($mobile_refresh);

  if ($_POST['terms'] == 'Yes, I would also like to receive automated texts from Allergan and its partners about treatments, services, and discounts that may be of interest to me. Message and data rates may apply. Consent is not required for sign-up. You can opt out at any time. <strong>To continue receiving texts about this offer, including confirmation texts, you must opt-in.</strong>') $opt_in_status = 'true';
  // Creds
  $api_user = 'adluser';
  $api_secret = 'adf#fl*md9762';
  date_default_timezone_set('America/Los_Angeles');
  $time_string = date("h:i:s a");
  $time_string = strtoupper($time_string);
  
  // Format String
  $clear_text = $api_user . "|" . $api_secret . "|" . $time_string;
  
  // Hash Secret
  $key = utf8_encode('A!!3rG@nPwDR3s3t');
  
  // Encrypt using Triple DES 
  $encrypt_text = openssl_encrypt($clear_text, "des-ede", $key, 0, "");
  
  // Base64 Encode encrypted data
  $access_token = base64_encode($encrypt_text);

 $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api3.brilliantdistinctionsprogram.com/api/Consumer/ClaimOffer/?accessToken=" . $access_token,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\r\n  \"FirstName\": \"" . $_POST['first_name'] . "\",\r\n  \"lastName\": \"" . $_POST['last_name'] . "\",\r\n  \"gender\": \"\",\r\n  \"email\": \"" . $_POST['email'] . "\",\r\n  \"ForcedEmail\": true,\r\n  \"mobilePhone\": \"" . $_POST['telclear'] . "\",\r\n  \"zip\": \"" . $_POST['zip_code'] . "\",\r\n  \"birthDate\": \"" . $birthdate_time . "\",\r\n  \"CID\": \"" . $_POST['cid'] . "\",\r\n  \"UTMTag\": \"\",\r\n  \"OfferCode\": \"BOTOXSAVEQ32019\",\r\n  \"DidAcceptTerms\": true,\r\n  \"DidAcceptOfferTerm\": $opt_in_status \r\n}",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "cache-control: no-cache"
  ),
));

$response_2 = curl_exec($curl);
$response_2 = json_decode($response_2,true);
$valid_submission = $response_2['IsValid'];
$post_outcome = $response_2['Message'];
$err = curl_error($curl);


curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} elseif (($valid_submission == true) && (strpos( $post_outcome, 'Existing User' ) !== false ) && (strpos( $post_outcome, 'success' ) !== false )) {
header("Location: https://www.ownyourlook.com/sms-offer/thank-you-registered/");
} elseif (($valid_submission == true) && ( $post_outcome == 'success' )) {
header("Location: https://www.ownyourlook.com/sms-offer/thank-you/");
} elseif (($valid_submission == false) && (strpos( $post_outcome, 'Phone Number and Email don\'t match' ) !== false )) {
header("Location: https://www.ownyourlook.com/sms-offer/?error=mismatch&tl=" . $mobile_refresh);
} elseif (($valid_submission == false) && (strpos( $post_outcome, 'Offer already claimed associated with this consumer' ) !== false )) {
header("Location: https://www.ownyourlook.com/sms-offer/?error=claimed");
} else 
header("Location: https://www.ownyourlook.com/sms-offer/tl=" . $mobile_refresh");
?>