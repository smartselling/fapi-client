<?php

namespace SmartSelling\FapiClient\CheckConnection;

use SmartSelling\HttpClient\HttpRequest;
use SmartSelling\HttpClient\HttpResponse;
use SmartSelling\HttpClient\MockHttpClient;

class FailCheckConnectionMockHttpClient extends MockHttpClient
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
				401,
				[
					'Date' => ['Tue, 20 Mar 2018 13:57:53 GMT'],
					'Server' => ['Apache/2.4.18 (Ubuntu)'],
					'X-Powered-By' => ['Nette Framework'],
					'X-Frame-Options' => ['SAMEORIGIN'],
					'Expires' => ['Thu, 19 Nov 1981 08:52:00 GMT'],
					'Cache-Control' => ['no-store, no-cache, must-revalidate'],
					'Pragma' => ['no-cache'],
					'Set-Cookie' => [
						'PHPSESSID=6rndq6itnjr0g0f2nv4gv3255q; expires=Tue, 03-Apr-2018 12:57:53 GMT; Max-Age=1206000; path=/; HttpOnly',
						'PHPSESSID=o1ptvcfeskveg1a7qb4tc9k3qv; expires=Tue, 03-Apr-2018 12:57:53 GMT; Max-Age=1206000; path=/; HttpOnly',
						'PHPSESSID=o1ptvcfeskveg1a7qb4tc9k3qv; expires=Tue, 03-Apr-2018 12:57:53 GMT; Max-Age=1206000; path=/; HttpOnly',
					],
					'Vary' => ['X-Requested-With'],
					'Content-Length' => ['58'],
					'Content-Type' => ['application/json; charset=utf-8'],
				],
				'{"error":{"message":"Invalid password."},"status":"error"}'
			)
		);
	}
}
