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

use core\model\AclTable;
use core\model\RoleTable;
use core\model\UserTable;
use wulaphp\auth\Passport;
use wulaphp\io\Request;

class AdminPassport extends Passport {

	public function is($roles) {
		$myroles = $this->data['roles1'];
		if (empty($myroles)) {
			return false;
		}

		return !empty(array_intersect($myroles, $roles));
	}

	/**
	 * 用户可以有多个角色，角色可以继承其它角色，被继承的角色权限优先级低于当前角色.
	 *
	 * @see table `acl`
	 *
	 * @param string $op    操作
	 * @param string $res   资源
	 * @param array  $extra 额外数据
	 *
	 * @return bool
	 */
	protected function checkAcl($op, $res, $extra) {
		static $checked = [];
		if (!isset($this->data['acls'])) {
			$this->loadAcl();
		}
		//未找到对应的ACL
		if (!$this->data['acls']) {
			return false;
		}
		$resid = $op . '@' . $res;
		if (isset($checked[ $resid ])) {
			return $checked[ $resid ];
		}
		$reses[] = $op . '@' . $res;
		// 对资源的全部操作授权
		$reses[] = '*@' . $res;
		$ress    = explode('/', $res);

		if (count($ress) > 1) {
			// 对上级资源的全部操作授权
			while ($ress) {
				array_pop($ress);
				$reses[] = '*@' . implode('/', $ress);
			}
		}
		// 对所有资源的全部操作授权，特别是网站拥有者
		$reses[] = '*@*';
		// 权限检测.
		foreach ($reses as $opres) {
			if (isset($this->data['acls'][ $opres ])) {
				$checked[ $resid ] = $this->data['acls'][ $opres ]['allowed'] ? true : false;

				return $checked[ $resid ];
			}
		}

		return false;
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
		$roleTable               = new RoleTable();
		foreach ($user['roles'] as $r) {
			$rid = $r['id'];
			$rs  = [0 => $r];
			$roleTable->select('id,upid,name')->recurse($rs);
			$this->data['roles'][ $rid ] = $rs;
			foreach ($rs as $rr) {
				$this->data['roles1'][] = $rr['name'];
			}
		}

		$this->data['roles1'] = array_unique($this->data['roles1']);

		$table->update(['lastip' => Request::getIp(), 'lastlogin' => time()], ['id' => $this->uid]);

		return true;
	}

	/**
	 * 加载ACL.
	 */
	private function loadAcl() {
		$acls   = [];
		$loaded = [];
		if ($this->data['roles']) {
			$acl = new AclTable();
			foreach ($this->data['roles'] as $roles) {
				foreach ($roles as $role) {
					if (isset($loaded[ $role['id'] ])) {
						continue;
					}
					$loaded[ $role['id'] ] = 1;
					$ac                    = $acl->findAll(['role_id' => $role['id']], 'op,res,allowed,priority');
					foreach ($ac as $a) {
						$res = $a['op'] . '@' . $a['res'];
						if (!isset($acls[ $res ]) || $acls[ $res ]['priority'] > $a['priority']) {
							$ra = $a->get();
							unset($ra['op'], $ra['res']);
							$acls[ $res ] = $ra;
						}
					}
				}
			}
		}
		$this->data['acls'] = $acls;
		$this->store();
	}
}