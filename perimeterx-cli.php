<?php 
	require_once __DIR__ . '/vendor/autoload.php';
	use Perimeterx\PerimeterxCLIWorker;

	$perimeterxConfig = [
	    'app_id' => 'PXMI1FuMjS',
	    'cookie_key' => '1qoEAOpl5KU/4Uq3CJQXdBsdYgIcltf6oGL1BFqKUJNx8FTj4Wrk/ad+N0s7sFDV',
	    'auth_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzY29wZXMiOlsicmlza19zY29yZSIsInJlc3RfYXBpIl0sImlhdCI6MTQ2NTkwOTQ2OCwic3ViIjoiUFhNSTFGdU1qUyIsImp0aSI6ImY4MjE4YzMyLTRiOGYtNDY1Ni1hNDZjLWJlMjZjMzhjODgzMiJ9.YNWqnHneG3HHegkLz0rq9G3m_NNEkPxOhUCTQBefQsU',
	    "module_enabled" => true,
	    "captcha_enabled" => true,
	    "encryption_enabled" => true,
	    "blocking_score" => 60,
	    "max_buffer_len" => 1,
	    "send_page_activities" => true,
	    "send_block_activities" => true,
	    "debug_mode" => false,
	    "module_mode" => 2,
	    "api_timeout" => 10,
	    "api_connect_timeout" => 10,
	//    "custom_block_handler" => function ($pxCtx) {
	//        $block_score = $pxCtx->getScore();
	//        $block_uuid = $pxCtx->getUuid();
	//        $action = $pxCtx->getBlockAction();
	//
	//        /* user defined logic comes here */
	//        error_log('px score for user is ' . $block_score);
	//        error_log('px recommended action for user is ' . $action);
	//        error_log('px page uuid is ' . $block_uuid);
	//    },
	    "perimeterx_server_host" => "http://localhost:6379/",

	    'custom_user_ip' => function () {
	        return '5.28.140.25';
	    },

	    "local_proxy" => false,
	];

	try {
		$worker = new PerimeterxCLIWorker($perimeterxConfig);
		$worker->start();
	}catch(Exception $e) {

	}

 ?>