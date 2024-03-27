<?php



class ViazizaPay

{

	protected static $endpoints = [

		'production' => 'https://pay.small-deals.com/api',

	];



	protected static $resources = [

		'orange' => 'om',

		'coinbase' => 'coinbase',

		'mtn' => 'momo',

		'cart' => 'cart',

	];

	public static function processPayment($public_key, $private_key, $data, $resource, $mode = 'production')
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => static::$endpoints[$mode] . '/' . static::$resources[$resource],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Basic ' . base64_encode($public_key . ':' . $private_key)
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);

		$result = json_decode($response, true);


		return $result;
	}





	public static function pull()

	{

		$payment = json_decode(file_get_contents('php://input'), true);

		return $payment;
	}



	public static function getAmountInLocal($amount, $currency)

	{

		$context_options = array(

			'http' => array(

				'ignore_errors' => true,

				'method' => 'GET',

				'header' => 'Content-Type: application/json'

			)

		);

		$api_url = "https://pro-api.coinmarketcap.com/v2/tools/price-conversion";

		$api_key = "c499cf1f-d3a7-4663-9329-8c755e59b3c4";

		$api_url .= "?CMC_PRO_API_KEY={$api_key}&amount={$amount}&symbol={$currency}&convert=EUR";

		$context = stream_context_create($context_options);

		$response = json_decode(file_get_contents($api_url, false, $context), true);





		if (isset($response['data'])) {

			return round(650 * (float)$response['data'][0]['quote']['EUR']['price'], 2);
		}

		return false;
	}
}
