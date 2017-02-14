<?php
/*
 * admin类型的通行证.
 *
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace core\classes;

use core\model\UserTable;
use wulaphp\auth\Passport;
use wulaphp\io\Request;

class AdminPassport extends Passport {

	public function is($roles) {
		$myroles = $this->data['roles'];
		if (empty($myroles)) {
			return false;
		}

		return !empty(array_intersect($myroles, $roles));
	}

	protected function checkAcl($op, $res, $extra) {
		return true;
	}

	protected function doAuth($data = null) {
		list($username, $password) = $data;
		$table = new UserTable();
		$user  = $table->get(['username' => $username]);
		if ($user['username'] != $username) {
			$this->error = '用户名或密码错';

			return false;
		}
		$passwdCheck = Passport::verify($password, $user['hash']);
		if (!$passwdCheck) {
			$this->error = '用户名或密码错';

			return false;
		}
		$status = $user['status'];
		if ($status == '0') {
			$this->error = '你已经被禁用，请联系管理员.';

			return false;
		}

		$this->uid               = $user['id'];
		$this->username          = $user['username'];
		$this->nickname          = $user['nickname'];
		$this->data['status']    = $user['status'];
		$this->data['lastip']    = $user['lastip'];
		$this->data['lastlogin'] = $user['lastlogin'];

		foreach ($user['roles'] as $r) {
			$this->data['roles'][] = $r['name'];
		}

		$table->update(['lastip' => Request::getIp(), 'lastlogin' => time()], ['id' => $this->uid]);

		return true;
	}
}