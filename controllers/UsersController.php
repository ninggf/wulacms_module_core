<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace core\controllers;

use core\form\AdminForm;
use core\model\RoleTable;
use core\model\UserTable;
use dashboard\classes\BackendController;
use wula\ui\classes\BootstrapFormRender;
use wulaphp\app\App;
use wulaphp\auth\Passport;
use wulaphp\db\DatabaseConnection;
use wulaphp\io\Ajax;
use wulaphp\validator\JQueryValidatorController;
use wulaphp\validator\ValidateException;

/**
 * 管理员控制器.
 *
 * @package core\controllers
 * @accept  core\form\AdminForm
 * @acl     m:system/account
 */
class UsersController extends BackendController {
	use JQueryValidatorController;

	public function index() {
		$data           = [];
		$roleM          = new RoleTable();
		$data['roles']  = $roleM->findAll(null, 'id,name')->limit(0, 500)->asc('id');
		$data['canAcl'] = $this->passport->cando('acl:system/account');

		return view($data);
	}

	public function users() {
		return view();
	}

	public function roles() {
		$roleM          = new RoleTable();
		$data['roles']  = $roleM->findAll(null, 'id,name')->limit(0, 500)->asc('id');
		$data['canAcl'] = $this->passport->cando('acl:system/account');

		return view($data);
	}

	public function data($status = '', $q = '', $rid = '', $count = '') {
		$model = new UserTable();
		$where = ['id >' => 1];
		if ($status == '0') {
			$where['status'] = $status;
		} else {
			$where['status'] = 1;
		}

		if ($q) {
			$where1['username LIKE']   = '%' . $q . '%';
			$where1['||nickname LIKE'] = '%' . $q . '%';
			$where[]                   = $where1;
		}
		$users = $model->select('User.*');
		if ($rid) {
			$users->join('{user_role} AS UR', 'User.id = UR.user_id');
			$where['role_id'] = $rid;
		}
		$users->where($where)->page()->sort();

		$total = '';
		if ($count) {
			$total = $users->total('id');
		}
		$data['items'] = $users;
		$data['total'] = $total;

		return view($data);
	}

	public function edit($id = '') {
		$form = new AdminForm();
		if ($id) {
			$admin = $form->get($id);
			$user  = $admin->get(0);
			if ($id != 1) {
				$user['roles'] = $admin->roles()->toArray('id');
			}
			$form->inflateByData($user);
			$form->removeRule('password', 'required');
		}
		$data['form']  = BootstrapFormRender::v($form);
		$data['id']    = $id;
		$data['rules'] = $form->encodeValidatorRule($this);

		return view($data);
	}

	public function savePost($id) {
		$form = new AdminForm();
		$user = $form->inflate();
		try {
			if ($id) {
				$form->removeRule('password', 'required');
			}
			if ($id == '1') {
				$form->removeRule('roles');
				unset($user['roles'], $user['status']);
			}
			$form->validate($user);
			if (($id && $user['password']) || !$id) {
				$user['hash'] = Passport::passwd($user['password']);
			}
			unset($user['password'], $user['password1']);
			if ($id) {
				$rst = $form->updateAccount($user);
			} else {
				unset($user['id']);
				$rst = $form->newAccount($user);
			}
			if (!$rst) {
				return Ajax::error($form->lastError());
			}
		} catch (ValidateException $ve) {
			return Ajax::validate('AdminForm', $ve->getErrors());
		} catch (\PDOException $pe) {
			return Ajax::error($pe->getMessage());
		}

		return Ajax::reload('#core-admin-table', $id ? '用户修改成功' : '新用户已经成功创建');
	}

	public function setStatus($status, $ids = '') {
		$ids = safe_ids2($ids);
		if ($ids) {
			$status = $status === '1' ? 1 : 0;
			$idkey  = array_search('1', $ids);
			if ($idkey !== false) {
				unset($ids[ $idkey ]);
			}
			if ($ids) {
				App::db()->update('{user}')->set(['status' => $status])->where(['id IN' => $ids])->exec();
			}

			return Ajax::reload('#core-admin-table', $status == '1' ? '所选用户已激活' : '所选用户已禁用');
		} else {
			return Ajax::error('未指定用户');
		}
	}

	/**
	 * 删除用户.
	 *
	 * @param string $ids
	 *
	 * @acl d:system/account
	 * @return \wulaphp\mvc\view\JsonView
	 */
	public function del($ids = '') {
		$ids = safe_ids2($ids);
		if ($ids) {
			$idkey = array_search('1', $ids);
			if ($idkey !== false) {
				unset($ids[ $idkey ]);
			}
			if ($ids) {
				$error = '';

				$rst = App::db()->trans(function (DatabaseConnection $db) use ($ids) {
					if (!$db->delete()->from('{user}')->where(['id IN' => $ids])->exec()) {
						return false;
					}

					return true;
				}, $error);

				if ($rst) {
					return Ajax::reload('#core-admin-table', '所选用户已删除');
				} else {
					return Ajax::error($error ? $error : '删除用户出错，请找系统管理员');
				}
			}
		}

		return Ajax::error('未指定用户');
	}
}