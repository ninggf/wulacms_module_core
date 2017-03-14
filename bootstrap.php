<?php
namespace core;

use core\classes\AdminPassport;
use dashboard\classes\DashboardUI;
use wula\cms\CmfModule;
use wulaphp\app\App;
use wulaphp\auth\Passport;
use wulaphp\io\Response;
use wulaphp\router\Router;

class CoreModule extends CmfModule {
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
		$system = $ui->getMenu('system');
		$module = $system->getMenu('module');

		$account       = $system->getMenu('account');
		$account->name = '账户';
		$account->pos  = 1;
		$account->icon = 'fa fa-id-card-o';

		$module->name = '模块';
		$module->icon = 'fa fa-cubes';
		$module->pos  = 10;

	}

	public function getVersionList() {
		$v['1.0.0'] = '';

		return $v;
	}

	/**
	 * @bind router\beforeDispatch
	 */
	public static function beforeDispatch($router) {
		if (!WULACMF_INSTALLED) {
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