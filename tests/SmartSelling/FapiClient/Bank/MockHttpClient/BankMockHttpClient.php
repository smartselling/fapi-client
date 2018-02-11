<?php

namespace SmartSelling\FapiClient\Bank;

use SmartSelling\HttpClient\HttpRequest;
use SmartSelling\HttpClient\HttpResponse;
use SmartSelling\HttpClient\MockHttpClient;


class BankMockHttpClient extends MockHttpClient
{
	public function __construct()
	{
		$this->add(
			new HttpRequest(
				'http://api.fapi.log/v2/banks/1',
				'GET',
				array(
					'auth' => array('tester@fapi.cz', 'xxx'),
					'headers' => array('Content-Type' => 'application/json', 'Accept' => 'application/json'),
				)
			),
			new HttpResponse(
				200,
				array(
					'Date' => array('Tue, 20 Mar 2018 13:57:56 GMT'),
					'Server' => array('Apache/2.4.18 (Ubuntu)'),
					'X-Powered-By' => array('Nette Framework'),
					'X-Frame-Options' => array('SAMEORIGIN'),
					'Expires' => array('Thu, 19 Nov 1981 08:52:00 GMT'),
					'Cache-Control' => array('no-store, no-cache, must-revalidate'),
					'Pragma' => array('no-cache'),
					'Set-Cookie' => array(
						'PHPSESSID=keiq7ger9pjoqiu6k05v0k9i7j; expires=Tue, 03-Apr-2018 12:57:56 GMT; Max-Age=1206000; path=/; HttpOnly',
						'PHPSESSID=hpedeq2e3v1qbp8rguhkq7pi0h; expires=Tue, 03-Apr-2018 12:57:56 GMT; Max-Age=1206000; path=/; HttpOnly',
						'PHPSESSID=hpedeq2e3v1qbp8rguhkq7pi0h; expires=Tue, 03-Apr-2018 12:57:56 GMT; Max-Age=1206000; path=/; HttpOnly',
					),
					'Vary' => array('X-Requested-With'),
					'Content-Length' => array('76'),
					'Content-Type' => array('application/json; charset=utf-8'),
				),
				'{"id":1,"name":"Fio banka","api":true,"bank_code":"2010","swift":"FIOBCZPP"}'
			)
		);
		$this->add(
			new HttpRequest(
				'http://api.fapi.log/v2/banks',
				'GET',
				array(
					'auth' => array('tester@fapi.cz', 'xxx'),
					'headers' => array('Content-Type' => 'application/json', 'Accept' => 'application/json'),
				)
			),
			new HttpResponse(
				200,
				array(
					'Date' => array('Tue, 20 Mar 2018 13:57:56 GMT'),
					'Server' => array('Apache/2.4.18 (Ubuntu)'),
					'X-Powered-By' => array('Nette Framework'),
					'X-Frame-Options' => array('SAMEORIGIN'),
					'Expires' => array('Thu, 19 Nov 1981 08:52:00 GMT'),
					'Cache-Control' => array('no-store, no-cache, must-revalidate'),
					'Pragma' => array('no-cache'),
					'Set-Cookie' => array(
						'PHPSESSID=5rv0ug39t8jpncqv5lhrb25ps0; expires=Tue, 03-Apr-2018 12:57:56 GMT; Max-Age=1206000; path=/; HttpOnly',
						'PHPSESSID=ub1r12shmu6oe4p0ig9fmjkr9h; expires=Tue, 03-Apr-2018 12:57:56 GMT; Max-Age=1206000; path=/; HttpOnly',
						'PHPSESSID=ub1r12shmu6oe4p0ig9fmjkr9h; expires=Tue, 03-Apr-2018 12:57:56 GMT; Max-Age=1206000; path=/; HttpOnly',
					),
					'Vary' => array('X-Requested-With'),
					'Content-Length' => array('809'),
					'Content-Type' => array('application/json; charset=utf-8'),
				),
				'{"banks":[{"id":1,"name":"Fio banka","api":true,"bank_code":"2010","swift":"FIOBCZPP"},{"id":2,"name":"Komerční Banka","api":false,"bank_code":"0100","swift":"KOMBCZPP"},{"id":3,"name":"Raiffeisen Bank","api":false,"bank_code":"5500","swift":"RZBCCZPP"},{"id":4,"name":"ČSOB","api":false,"bank_code":"0300","swift":"CEKOCZPP"},{"id":5,"name":"GE Money Bank","api":false,"bank_code":"0600","swift":"AGBACZPP"},{"id":6,"name":"Air Bank","api":false,"bank_code":"3030","swift":"AIRACZPP"},{"id":7,"name":"Tatra Banka","api":false,"bank_code":"1100","swift":"TATRSKBX"},{"id":8,"name":"UniCredit Bank","api":false,"bank_code":"2700","swift":"BACXCZPP"},{"id":9,"name":"VÚB Banka","api":false,"bank_code":"0200","swift":"SUBASKBX"},{"id":10,"name":"Sberbank","api":false,"bank_code":"6800","swift":"VBOECZ2"}]}'
			)
		);
	}
}
