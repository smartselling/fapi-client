<?php

namespace SmartSelling\FapiClient\CheckConnection;

use SmartSelling\HttpClient\HttpRequest;
use SmartSelling\HttpClient\HttpResponse;
use SmartSelling\HttpClient\MockHttpClient;

class CheckConnectionMockHttpClient extends MockHttpClient
{

	public function __construct()
	{
		$this->add(
			new HttpRequest(
				'http://api.fapi.log/v2/check-connection',
				'GET',
				[
					'auth' => ['tester@fapi.cz', 'xxx'],
					'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
				]
			),
			new HttpResponse(
				200,
				[
					'Date' => ['Tue, 20 Mar 2018 21:46:07 GMT'],
					'Server' => ['Apache/2.4.18 (Ubuntu)'],
					'X-Powered-By' => ['Nette Framework'],
					'X-Frame-Options' => ['SAMEORIGIN'],
					'Vary' => ['X-Requested-With'],
					'Expires' => ['Thu, 19 Nov 1981 08:52:00 GMT'],
					'Cache-Control' => ['no-store, no-cache, must-revalidate'],
					'Pragma' => ['no-cache'],
					'Set-Cookie' => [
						'PHPSESSID=m0b3vn8s5966k3fjadn5duarv8; expires=Tue, 03-Apr-2018 20:46:07 GMT; Max-Age=1206000; path=/; HttpOnly',
						'PHPSESSID=6cf0squgoroo7b281inptt9p2c; expires=Tue, 03-Apr-2018 20:46:07 GMT; Max-Age=1206000; path=/; HttpOnly',
						'PHPSESSID=6cf0squgoroo7b281inptt9p2c; expires=Tue, 03-Apr-2018 20:46:07 GMT; Max-Age=1206000; path=/; HttpOnly',
					],
					'Content-Length' => ['15'],
					'Content-Type' => ['application/json; charset=utf-8'],
				],
				'{"status":"ok"}'
			)
		);
	}
}
