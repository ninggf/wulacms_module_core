<?php

namespace core;

use core\classes\AdminPassport;
use dashboard\classes\DashboardUI;
use wula\cms\CmfModule;
use wulaphp\app\App;
use wulaphp\auth\Passport;
use wulaphp\io\Response;
use wulaphp\router\Router;

trait CoreHooks {
	/**
	 * @param \wulaphp\auth\AclResourceManager $manager
	 *
	 * @bind rbac\initAdminManager
	 */
	public static function aclRes($manager) {
		$manager->getResource('system', '系统管理', 'm');

		$res = $manager->getResource('system/setting', '设置', 'm');
		$res->addOperate('default', '通用设置');

		$res = $manager->getResource('system/account', '管理员', 'm');
		$res->addOperate('acl', '授权');

		$manager->getResource('system/module', '模块', 'm');

	}

	/**
	 * 管理员表格.
	 *
	 * @param array $cols
	 *
	 * @filter  get_columns_of_core.admin.table
	 * @return array
	 */
	public static function adminTableColumns($cols) {
		$cols['roles']     = [
			'name'   => '角色',
			'show'   => true,
			'width'  => 120,
			'order'  => 10,
			'render' => function ($v) {
				$rs = [];
				foreach ($v as $r) {
					$rs[] = $r['name'];
				}

				return implode(',', $rs);
			}
		];
		$cols['phone']     = ['name' => '手机', 'show' => true, 'width' => 120, 'order' => 20];
		$cols['email']     = ['name' => '邮箱', 'show' => false, 'order' => 30];
		$cols['lastlogin'] = [
			'name'   => '最后登录',
			'show'   => true,
			'width'  => 150,
			'sort'   => 'lastlogin',
			'order'  => 50,
			'render' => function ($v) {
				return date('Y-m-d H:i:s', $v);
			}
		];
		$cols['status']    = [
			'name'   => '激活',
			'show'   => true,
			'width'  => 60,
			'order'  => 60,
			'sort'   => 'status',
			'align'  => 'center',
			'render' => function ($v) {
				if ($v) {
					return '<span class="active"><i class="fa fa-check text-success text-active"></i></span>';
				} else {
					return '<span><i class="fa fa-times text-danger text"></i></span>';
				}
			}
		];

		return $cols;
	}
}

/**
 * Class CoreModule
 * @package core
 * @group   core
 */
class CoreModule extends CmfModule {
	use CoreHooks;

	public function getName() {
		return '内核';
	}

	public function getDescription() {
		return 'wulacms内容管理框架内核模块';
	}

	public function getHomePageURL() {
		return 'https://www.wulacms.com/modules/core';
	}

	/**
	 * @param Passport $passport
	 *
	 * @filter passport\newAdminPassport
	 *
	 * @return Passport
	 */
	public static function createAdminPassport($passport) {
		if ($passport instanceof Passport) {
			$passport = new AdminPassport();
		}

		return $passport;
	}

	/**
	 * @param DashboardUI $ui
	 *
	 * @bind dashboard\initUI
	 */
	public static function initDashboard(DashboardUI $ui) {
		$passport = whoami('admin');
		if ($passport->cando('m:system')) {
			$system = $ui->getMenu('system');
			if ($passport->cando('m:system/account')) {
				$account       = $system->getMenu('account');
				$account->name = '管理员';
				$account->url  = App::hash('~core/users');
				$account->pos  = 1;
				$account->icon = 'fa fa-id-card-o';
			}
			if ($passport->cando('m:system/module')) {
				$module        = $system->getMenu('module');
				$module->name  = '模块';
				$module->url   = App::hash('~core/module/installed');
				$module->icon  = 'fa fa-cubes';
				$module->pos   = 10;
				$module->badge = count(App::modules('upgradable'));

				$m            = $module->getMenu('installed');
				$m->name      = '已安装模块';
				$m->url       = $module->url;
				$m->iconStyle = 'color:green';
				$m->pos       = 1;

				$m       = $module->getMenu('new');
				$m->name = '未安装模块';
				$m->url  = App::hash('~core/module/uninstalled');
				$m->pos  = 2;

				if ($module->badge > 0) {
					$m            = $module->getMenu('up');
					$m->name      = '可升级模块';
					$m->badge     = $module->badge;
					$m->url       = App::hash('~core/module/upgradable');
					$m->iconStyle = 'color:orange';
					$m->pos       = 3;
				}

			}
		}
	}

	public function getVersionList() {
		$v['1.0.0'] = '初始化内核模块';
		$v['1.1.0'] = '添加user_gridcfg表';

		return $v;
	}

	/**
	 * @bind router\beforeDispatch
	 */
	public static function beforeDispatch($router) {
		if (!WULACMF_INSTALLED && defined('WULACMF_WEB_INSTALLER')) {
			$installURL = App::url('core/install');
			if (WWWROOT_DIR != '/') {
				$regURL = substr($installURL, strlen(WWWROOT_DIR) - 1);
			} else {
				$regURL = $installURL;
			}
			$regURL = ltrim($regURL, '/');
			if (!Router::is($regURL . '(/.*)?', true)) {
				Response::redirect($installURL);
			}
		}
	}
}

App::register(new CoreModule());