<?php
namespace App\Page;
use App\User;
use App\Room;
use App\Message;
use Datetime;

class Index extends \Gt\Page\Logic {

	public function go() {

		$this->id_room = $_GET['room'] ?? 1;

		$this->chatLog();
	}

	public function chatLog() {

		$room = new Room($this->id_room);
		$messages = $room->messageSearch();


		foreach ($messages as $m) {

			$user = $m->id_user;
			$date = new Datetime($m->createdAt);
			$date = $date->format('d/m/Y H:i:s');

			$message = $m->message;


			$t = $this->template->get('message');
			$t->textContent = "{$date} - {$user} : {$message}";
			$t->insertTemplate();
		}
	
		$this->document->querySelector('[name="id_room"]')->value = $this->id_room;
	}

	public function do_message ($data) {
		$data['id_user'] = $_SESSION['user_id'];
		$message = new Message();
		$message->create($data);
		header('Location: ./');
		die;
	}
}
