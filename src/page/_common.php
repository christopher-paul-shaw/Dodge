<?php
namespace App\Page;
use App\User;
use Gt\Response\Headers;

class _Common extends \Gt\Page\Logic {

	public function go() {
		$this->handleLogin();
		$this->navigation();
	}

	public function navigation () {
		if (User::isLoggedIn()) {
			$t = $this->template->get('logged-in');
			if (!is_null($t)) {
				$t->insertTemplate();
			}

			$user = new User(1);
			$name = $user->getValue('name');

			$nameElement = $this->document->querySelectorAll('.php-user-name');
			foreach ($nameElement as $e) {
				$e->textContent = $name;
			}
		}

		if (User::isAdmin()) {
			$t = $this->template->get('admin');
			if (!is_null($t)) {
				$t->insertTemplate();
			}
		}
	}


	public function handleLogin() {

		if (strstr(strtolower($_SERVER['REQUEST_URI']), '/auth/')) {
			return;
		}

		if (!User::isLoggedIn()) {
			Headers::redirect("/auth/login");
			die;
		}

	}

}
