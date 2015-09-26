<?php

return [

	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => 'Session',

	/**
	 * Consumers
	 */
	'consumers' => [

		'Google' => [
			'client_id'     => '42408787371-b8v7e68np26evsae27vlt71adeol75tn.apps.googleusercontent.com',
			'client_secret' => 'BPjysUr2bspIIh2bJM3qMuD7',
			'scope'         => ['calendar'],
		],

	]

];