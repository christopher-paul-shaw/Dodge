<?php
namespace App\Page;
use App\User;
use Gt\Response\Headers;
use Exception;

class Users extends \Gt\Page\Logic {

	public $options = [
				'permission' => [
						'admin' => 'Admin',
						'user' => 'User'
				]
		];

	public function go() {

		if (!User::isAdmin()) {
			Headers::redirect("/");
			die;
		}

		$this->outputPage();
		$this->outputUserList();
	}


	public function outputPage () {

		$e = $this->document->querySelector('.php-add-permissions');
		if ($e) {
			if ($e && $this->options['permission']) {
				foreach ($this->options['permission'] as $o) {
					$ele = $this->document->createElement('option');
					$ele->value = $o;
					$ele->textContent = $o;
					if ($o == 'User') {
						$ele->setAttribute('selected','selected');
					}
					$e->appendChild($ele);
				}
			}
		}

	}
	public function outputUserList () {

		$user = new User();
		$list = $user->search();

		foreach ($list as $u) {
			$t = $this->template->get('user-row');
			$t = $this->fillForm($t,$u);
			$t->insertTemplate();
		}	

	}		

	public function fillForm ($node, $data) {

		$current = new User($data->id_user);
		foreach ($data as $key => $v) {

			$elements = $node->querySelectorAll("[name='{$key}'],.php-{$key}");
			foreach ($elements as $e) {
				$e->value = $v;
			}

			$select = $node->querySelector("select[name='{$key}']");
			if ($select && $this->options[$key]) {
				foreach ($this->options[$key] as $o) {
					$ele = $this->document->createElement('option');
					$ele->value = $o;
					$ele->textContent = $o;
					if (strtolower($o) == strtolower($current->getValue($key))) {
						$ele->setAttribute('selected','selected');
					}
					$select->appendChild($ele);
				}

			}

		}

		return $node;
	}

	public function do_edit ($data) {
		
		if (empty($data['id_user'])) return;

		$current = new User($data['id_user']);

		if (isset($data['delete'])) {
			$current->delete();
			Headers::redirect("/users");
		}

		if (empty($data['password'])) {
			unset($data['password']);
		}

		$current->setValue($data);

		Headers::redirect("/users");
	}

	public function do_add ($data) {
		try {
			$user = new User($data['email']);
			$user->create($data);
			Headers::redirect("/users");
			die;
		}
		catch (Exception $e) {
			$t = $this->template->get("error");
			$t->textContent = $e->getMessage();
			$t->insertTemplate();
			$this->document->querySelector('.php-add-show')->setAttribute("checked","checked");
		}
	}

}