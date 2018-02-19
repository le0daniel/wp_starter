<?php

return [

	/**
	 * Add your service providers below,
	 * They must implement the ServiceProvider Contract,
	 * Should extend the abstract class RootServiceProvider
	 */
	'providers'=>[
		\le0daniel\System\ServiceProviders\Log::class,
	],

	/* If you want to use classes as controllers */
	'map_controllers_to_classes'=>false,
];