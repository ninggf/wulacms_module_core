<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace core\model;

use wulaphp\auth\Passport;
use wulaphp\db\DatabaseConnection;
use wulaphp\form\FormTable;
use wulaphp\io\Request;
use wulaphp\validator\JQueryValidator;

class UserTable extends FormTable {
	use JQueryValidator;
	/**
	 * @type int
	 */
	public $id;
	/**
	 * 登录账户(<b class="text-danger">*</b>)
	 * @var \wula\ui\classes\TextField
	 * @type string
	 * @required
	 * @callback (checkUsername(id)) => Account exist
	 * @layout 2, col-xs-6
	 */
	public $username;

	/**
	 * 昵称(<b class="text-danger">*</b>)
	 * @var \wula\ui\classes\TextField
	 * @type string
	 * @required
	 * @layout 2, col-xs-6
	 */
	public $nickname;
	/**
	 * 手机号
	 * @var \wula\ui\classes\TextField
	 * @type string
	 * @phone
	 * @layout 3, col-xs-6
	 */
	public $phone;
	/**
	 * 邮件地址
	 * @var \wula\ui\classes\TextField
	 * @type string
	 * @email
	 * @layout 3,col-xs-6
	 */
	public $email;

	public function roles() {

		return $this->belongsToMany(new RoleTable($this), 'user_role');
	}

	/**
	 * 更新用户最后登录信息.
	 *
	 * @param int $uid
	 */
	public function updateLoginInfo($uid) {
		if ($uid) $this->update(['lastip' => Request::getIp(), 'lastlogin' => time()], ['id' => $uid]);
	}

	/**
	 * 更新账户信息.
	 *
	 * @param array $data
	 *
	 * @return bool 更新成功返回true,反之返回false.
	 *
	 * @throws \wulaphp\validator\ValidateException
	 * @throws  \PDOException
	 */
	public function updateAccount($data) {
		if (isset($data['roles'])) {
			$rst = $this->trans(function (DatabaseConnection $db) use ($data) {
				$id    = $data['id'];
				$roles = $data['roles'];
				unset($data['roles']);
				if (!$this->update($data, ['id' => $id])) {
					return false;
				}
				if (!$db->delete()->from('{user_role}')->where(['user_id' => $id])->exec()) {
					return false;
				}
				if ($roles) {
					$rs = [];
					array_unique($roles);
					foreach ($roles as $rid) {
						$rs[] = ['user_id' => $id, 'role_id' => $rid];
					}
					if (!$db->insert($rs, true)->into('{user_role}')->exec()) {
						return false;
					}
				}
				return true;
			});
			return $rst;
		} else {
			$id = $data['id'];
			return $this->update($data, ['id' => $id]);
		}
	}

	public function newAccount($data) {

		$id = $this->trans(function (DatabaseConnection $db) use ($data) {
			if (isset($data['roles'])) {
				$roles = $data['roles'];
				unset($data['roles']);
			} else {
				$roles = [];
			}
			$id = $this->insert($data);
			if (!$id) {
				return false;
			}
			if ($roles) {
				$rs = [];
				array_unique($roles);
				foreach ($roles as $rid) {
					$rs[] = ['user_id' => $id, 'role_id' => $rid];
				}
				if (!$db->insert($rs, true)->into('{user_role}')->exec()) {
					return false;
				}
			}

			return $id;
		});

		return $id;
	}

	/**
	 * 更新用户密码.
	 *
	 * @param int    $id       用户ID
	 * @param string $password 密码(明文)
	 *
	 * @return bool
	 * @throws  \PDOException
	 */
	public function chagnePassword($id, $password) {
		return $this->update(['hash' => Passport::passwd($password)], ['id' => $id]);
	}

	public function checkUsername($value, $data, $msg) {
		$id                = unget($data, 'id');
		$where['username'] = $value;
		if ($id) {
			$where['id <>'] = $id;
		}

		if ($this->exist($where)) {
			return $msg;
		}

		return true;
	}
}