<?php
	namespace Perimeterx;

	class PerimeterxCLIWorker extends Perimeterx {
		/**
		* @var PerimeterxLogger
		*/
		private $_logger; 

		/**
		* @var PerimeterxHttpClient
		*/
		private $_http;

		/**
		* @var PerimeterxQueueClient
		*/
		private $_queueClient;

		/**
		* Flag - if the worker is starting.
		* @var boolean
		*/
		private $_workerStarted;

		public function __construct(array $pxConfig) {
			parent::__construct($pxConfig);

			$this->_queueClient = new PerimeterxQueueClient($this->pxConfig);
			$this->_http = new PerimeterxHttpClient($this->pxConfig);
			$this->_logger = $this->pxConfig["logger"];
		}

		public function start() {
			$this->_workerStarted = true;
			while($this->_workerStarted) {
				while(!$this->_queueClient->isEmpty()) {
					try{
						$message = $this->_queueClient->receive();
						$this->_handlePXMessage($message);
					}catch(PerimeterxException $e) {
						//@TODO - Write log to logger
					}
				}
			}	
		}

		private function _handlePXMessage($message) {
			if(!$message || !is_array($message)) {
				return false;
			}

			$msg_type = $message["message_type"];
			$payload = $message["message"];

			//Validation message
			if(!is_array($payload) || !isset($payload["http"]) || !isset($payload["activities"])) {
				return false;
			}

			//Send message
			$this->httpClient->send($payload["http"]["path"], $payload["http"]["method"], $payload["activities"], $payload["http"]["method"], $this->pxConfig['api_timeout'], $this->pxConfig['api_connect_timeout']);    
		}

	}
?>