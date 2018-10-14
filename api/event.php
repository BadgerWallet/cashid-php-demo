<?php
	// Set a max time we can run this script in seconds. [currently 1 day]
	define('MAX_TIME_BEFORE_RECONNECT', 86400);
	// Allow PHP to run for a given period of time.
	ini_set('max_execution_time', MAX_TIME_BEFORE_RECONNECT);

	ob_end_clean();
	gc_enable();

	// Shut down if client disconnects
	ignore_user_abort(false);

	// Set the output headers to event-stream and encoding to utf-8.
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	header('Access-Control-Allow-Methods: GET');
	header('Access-Control-Expose-Headers: X-Events');  

	// Declare a class for the event system.
	class event_system
	{
		// Store a private ID for each message.
		private $id = 0;

		function __construct()
		{
			// If the headers contain a last-sent-id..
			if(isset($_SERVER['HTTP_LAST_EVENT_ID']))
			{
				// Start counting from the previous id.
				$this->id = $_SERVER['HTTP_LAST_EVENT_ID'];
			}
			
			// Configure the retry time to 0.5 seconds.
			$this->send_retry_time(1000);
		}

		// Send a retry time configuration change to the browser.
		function send_retry_time($time)
		{
			// Put the retry event and value into the output buffer.
			echo "event: set_retry_time" . PHP_EOL;
			echo "data: Set reconnection time to {$time} ms" . PHP_EOL;
			echo "retry: {$time}" . PHP_EOL;
			
			// Put an end of line to end the event.
			echo PHP_EOL;
			
			// Send the output buffer to the browser.
			if(ob_get_level())
			{
				ob_flush();
			}
			flush();
		}

		// Send a message to the browser.
		function send_event($name, $data)
		{
			// Increase the event id by one.
			$this->id += 1;

			// Put the event into the output buffer.
			echo "id: {$this->id}" . PHP_EOL;
			echo "event: {$name}" . PHP_EOL;
			echo "data: {$data}" . PHP_EOL;
			
			// Put an end of line to end the event.
			echo PHP_EOL;
			
			// Send the output buffer to the browser.
			if(ob_get_level())
			{
				ob_flush();
			}
			flush();
		}
	}

	// Create an instance of the event system.
	$event_system = new event_system();

	$timer = 0;

	// Start an endless loop.
	// while(true)
	// {
		// Check if a response exist.
		if(apcu_exists("cashid_response_{$_GET['x']}"))
		{
			// Copy the response data.
			$response_data = apcu_fetch("cashid_response_{$_GET['x']}");

			// Delete the response from disk.
			apcu_delete("cashid_response_{$_GET['x']}");

			// Send the response to the client.
			$event_system->send_event('response', json_encode($response_data));
		}

		// Check if a response exist.
		if(apcu_exists("cashid_confirmation_{$_GET['x']}"))
		{
			// Copy the response data.
			$confirmation_data = apcu_fetch("cashid_confirmation_{$_GET['x']}");

			// Delete the response from disk.
			apcu_delete("cashid_confirmation_{$_GET['x']}");

			// Send the response to the client.
			$event_system->send_event('confirmation', json_encode($confirmation_data));
		}

		// Since PHP might be configured to listen to client aborts,
		// but doesn't respond to them until flushing the output buffer..
		if($timer > 30)
		{
			// Send the current time to the browser.
			$event_system->send_event('server_time', date('H:i:s'));

			// reset the timer.
			$timer = 0;
		}
		else
		{
			$timer += 1;
		}

		// Sleep for one seconds.
	// 	usleep(1000000);
	// }
?>
