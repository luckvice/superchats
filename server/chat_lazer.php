<?php
//////ignore_user_abort(TRUE);
set_time_limit(0);
session_start();
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
error_reporting(E_ALL ^ E_NOTICE);  
//require_once '/var/www/html/superchats/vendor/autoload.php';
require_once '../vendor/autoload.php';
class Chat implements MessageComponentInterface {
	protected $clients;
	protected $users;

	public function __construct() {
		$this->clients = new \SplObjectStorage;

	}

	public function onOpen(ConnectionInterface $conn) {
		echo nl2br('[DEBUG]: Cliente conectou no WebSocket').PHP_EOL;
		//adding each websocket connection to $client
		$this->clients->attach($conn);
		// $this->users[$conn->resourceId] = $conn;
	}
	

	public function onClose(ConnectionInterface $conn) {
		$this->clients->detach($conn);
		echo nl2br('[DEBUG]: Cliente saiu do servidor.').PHP_EOL;
		// unset($this->users[$conn->resourceId]);
	}

	public function onMessage(ConnectionInterface $from,  $data) {
		$from_id = $from->resourceId;
		$data = json_decode($data);
		$type = $data->type;

		switch ($type) {
			case 'chat':
				$user_id = $data->user_id;
				$chat_msg = $data->chat_msg;
						
				//Mensagem de entrada
				$response_from = "<strong >Você:</strong>" . $chat_msg. "<br><br>";
	
				echo nl2br('[DEBUG SEND]: '.$user_id.' Mensagem ['.$chat_msg.'] | '.date("h:i:sa").' ').PHP_EOL;
				//mensagem para todos do site
				$response_to = "<a href='profile.php?name=". $user_id. "' target='_blank'> <img width='50px' height='50px' src='".$img_perfil."'> </a>: ".$user_id.":".$chat_msg."<br><br>"; 
				
				// Saida do chat todos os membros
				if($user_id=='')
				{
					$response_from="<b>Vocẽ entrou no SuperChats - Categoria Lazer </b><br><br>";
					
				}	
				
				//Enviando mensagem para você
				$from->send(json_encode(array("type"=>$type,"msg"=>$response_from)));
				
				//Enviando mensagem para os clientes do chat
				foreach($this->clients as $client)
				{
					if($from!=$client)
					{
						$client->send(json_encode(array("type"=>$type,"msg"=>$response_to)));
						echo nl2br('[DEBUG ALL]: '.$response_to).PHP_EOL;
					}
				}
				break;
		}
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		$conn->close();
	}

	
}
$server = IoServer::factory(
	new HttpServer(new WsServer(new Chat())),
	8001
);
$server->run();
/////ob_flush();
/////flush();
/////str_repeat("", 1500);
/////sleep(2);
?>