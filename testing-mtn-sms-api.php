<?php

$curl = curl_init(); 
$random_t= random_int(400,999)
$cn_id = "res_00".random_int
curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.mtn.com/v1/messages/sms/outbound",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n  \"senderAddress\": \"Flash\",\n  \"receiverAddress\": [\n    \"233241847113\"\n  ],\n  \"message\": \"Testing my sms\",\n  \"clientCorrelator\": \" ".$cn_id."\"\n}",
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer W6EY4s7ygW9hLbcdsxNRquGFnGoSdUZqLWalFIz6ead80txE",
    "Content-Type: Application/json",
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}