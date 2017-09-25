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

use core\model\AclTable;
use core\model\RoleTable;
use dashboard\classes\BackendController;
use wula\ui\classes\BootstrapFormRender;
use wulaphp\app\App;
use wulaphp\auth\AclResourceManager;
use wulaphp\io\Ajax;
use wulaphp\validator\JQueryValidatorController;
use wulaphp\validator\ValidateException;

/**
 * 角色控制器
 *
 * @package    core\controllers
 * @acl        m:system/account
 * @accept     core\model\RoleTable
 */
class RoleController extends BackendController {
	use JQueryValidatorController;

	/**
	 * @param string $id
	 *
	 * @return \wulaphp\mvc\view\SmartyView
	 */
	public function edit($id = '') {
		$data = ['id' => $id];
		$form = new RoleTable();
		if ($id) {
			$form->inflateFromDB(['id' => $id]);
		}
		$data['rules'] = $form->encodeValidatorRule($this);
		$data['form']  = BootstrapFormRender::v($form);

		return view($data);
	}

	public function savePost($id) {
		$form = new RoleTable();
		$role = $form->inflate();
		try {
			if ($id) {
				$rst = $form->updateRole($role);
			} else {
				unset($role['id']);
				$rst = $form->createRole($role);
			}
			if ($rst) {
				return Ajax::reload('#core-role-list', $id ? '角色已经更新' : '新的角色已经添加');
			}
		} catch (ValidateException $ve) {
			return Ajax::validate('RoleForm', $ve->getErrors());
		} catch (\PDOException $pe) {
			return Ajax::error('数据库出错:' . $pe->getMessage());
		}

		return Ajax::error('保存角色数据时出错了，请联系管理员');
	}

	public function del($id) {
		if (empty($id)) {
			return Ajax::error('未指定要删除的角色');
		}
		if ($id < 3) {
			return Ajax::error('系统内置的角色不能删除');
		}
		$roleM = new RoleTable();
		$rst   = $roleM->deleteRole($id);
		if ($rst) {
			return Ajax::reload('#core-role-list', '角色已经删除');
		} else {
			return Ajax::error('无法删除角色:' . $roleM->lastError());
		}
	}

	/**
	 * @param $id
	 *
	 * @acl acl:system/account
	 * @return \wulaphp\mvc\view\SmartyView
	 */
	public function acl($id) {
		if (empty($id)) {
			Ajax::fatal('角色ID为空');
		}
		$roleM = new RoleTable();
		$role  = $roleM->get($id);
		if (!$role['id']) {
			Ajax::fatal('角色不存在');
		}
		$data['role'] = $role;

		$aclResources      = AclResourceManager::getInstance('admin');
		$node              = $aclResources->getResource();
		$nodes             = $node->getNodes();
		$data ['ops']      = $node->getOperations();
		$data ['nodes']    = $nodes;
		$data ['acl']      = $role->acls()->toArray('allowed', 'res');
		$data ['parent']   = '/';
		$data ['debuging'] = APP_MODE != 'pro';

		return view($data);
	}

	/**
	 * @param        $id
	 * @param string $parentId
	 *
	 * @acl acl:system/account
	 * @return \wulaphp\mvc\view\SmartyView
	 */
	public function acldata($id, $parentId = '') {
		if (empty($id)) {
			Ajax::fatal('角色ID为空');
		}
		$roleM = new RoleTable();
		$role  = $roleM->get($id);
		if (!$role['id']) {
			Ajax::fatal('角色不存在');
		}
		$aclResources      = AclResourceManager::getInstance('admin');
		$node              = $aclResources->getResource($parentId);
		$nodes             = $node->getNodes();
		$data ['ops']      = $node->getOperations();
		$data ['nodes']    = $nodes;
		$data ['acl']      = $role->acls()->toArray('allowed', 'res');
		$data ['parent']   = $parentId;
		$data ['debuging'] = APP_MODE != 'pro';

		return view($data);
	}

	/**
	 * @param $role_id
	 * @param $acl
	 *
	 * @acl acl:system/account
	 * @return \wulaphp\mvc\view\JsonView
	 */
	public function setAcl($role_id, $acl) {
		$id = intval($role_id);
		if (empty($id)) {
			Ajax::fatal('角色ID为空');
		}
		$roleM = new RoleTable();
		$role  = $roleM->get($id);
		if (!$role['id']) {
			Ajax::fatal('角色不存在');
		}
		$priority       = 999 - intval($role['level']);
		$rst            = true;
		$beDeleteingIds = [];

		if (is_array($acl) && !empty ($acl)) {
			$acls     = [];
			$aclTable = new AclTable();
			$db       = App::db();
			foreach ($acl as $key => $v) {
				if (!is_numeric($v)) {
					$beDeleteingIds [] = $key;
					continue;
				}
				$v       = $v === '1' ? 1 : 0;
				$data    = ['role_id' => $id, 'res' => $key];
				$allowed = $aclTable->get($data, 'allowed')['allowed'];
				if (is_numeric($allowed)) {
					if ($allowed != $v) {
						$db->update('{acl}')->set(['allowed' => $v])->where($data)->exec();
					}
				} else {
					$data ['allowed']  = $v;
					$data ['priority'] = $priority;
					$acls []           = $data;
				}
			}
			if (!empty ($beDeleteingIds)) {
				$db->delete()->from('{acl}')->where(['role_id' => $role_id, 'res IN' => $beDeleteingIds])->exec();
			}
			if ($acls) {
				$rst = $db->insert($acls, true)->into('{acl}')->exec();
			}
		}

		if ($rst) {
			return Ajax::success('『' . $role['name'] . '』已授权成功');
		}

		return Ajax::success('『' . $role['name'] . '』授权失败');
	}
}