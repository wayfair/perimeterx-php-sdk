<?php
	namespace Perimeterx;

	class PerimeterxQueueClient {
		const MAX_MESSAGE_LENGTH = 51200;
		const MESSAGE_QUEUE_ID = 68674435;
		const MESSAGE_TYPE = 10;
		
		/**
		* @var resource - shared memory(queue structure) with perineterx daemon process.
		*/
		private $_queue;

		private $_pxConfig; 

	    public function __construct($pxConfig) {
	    	$this->_pxConfig = $pxConfig;
	    	$this->_queue = msg_get_queue(static::MESSAGE_QUEUE_ID);
	    }

	    /**
	     * Send message to queue
	     * @param object| mixied $payload 
	     * @return string|true(boolean) - return string when this method get error and return true when this method succuss to enquque the message.
	     */
	    public function send($payload)
	    {
	    	$queue_data = $this->getData();

	    	if(!(isset($queue_data["msg_qbytes"]) && $queue_data["msg_qbytes"] <= $this->_pxConfig["max_bytes_messages_queue"])) {
	    		throw new PerimeterxException(PerimeterxException::$QUEUE_OVERFLOW);
	    		
	    	}

	        $errorcode = null;
	        $result = msg_send($this->_queue,static::MESSAGE_TYPE,$payload,true, true, $errorcode);
			
			if(!$result) {
				$this->logger->error('queue client code:' . $error_code);
            	return json_encode(['error_code' => $errorcode]);	
			}

			return true;			
	    }

	    /**
		* Check if the queue memory is empty
		* @return boolean - return true if the queue is empty and false when have message in queue.
	 	*/
	    public function isEmpty() {
	    	$data = $this->getData();

	    	if(!isset($data["msg_qnum"])) {
	    		return false;
	    	}else {
	    		return ($data["msg_qnum"] == 0);	
	    	}
	    	
	    }

	    /**
		* Get message from the memory queue.
		* @return array(message_type = number, message = object|mixed )
	    */
	    public function receive() {
			$msg_type = NULL;
			$msg = NULL;
	    	if(msg_receive($this->_queue, static::MESSAGE_TYPE, $msg_type, static::MAX_MESSAGE_LENGTH, $msg, false, MSG_IPC_NOWAIT)) {
	    		return array("message_type" => $msg_type, "message" => $msg);
	    	}else {
	    		return false;
	    	}
	    }

	    /**
		* Get statistics from the queue message(size, count message, etc)
		* @see http://php.net/manual/en/function.msg-stat-queue.php
		* @return array of data and if the quque is not initilize this function return empty array.
	    */
	    private function getData() {
	    	if(!$this->_queue) {
	    		return array();
	    	}
	    	
	    	return msg_stat_queue($this->_queue);
	    }
	}

?>