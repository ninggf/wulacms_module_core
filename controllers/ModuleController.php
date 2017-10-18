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

use dashboard\classes\BackendController;
use Michelf\MarkdownExtra;
use wulaphp\app\App;
use wulaphp\io\Ajax;
use wulaphp\util\ArrayCompare;

/**
 * Class ModuleController
 * @package core\controllers
 * @acl     m:system/module
 */
class ModuleController extends BackendController {
	public function index($type = 'installed') {
		$groups          = [];
		$modules         = App::modules($type);
		$upCount         = 0;
		$data['modules'] = [];
		foreach ($modules as $m) {
			$gp                = $m->group;
			$groups[ $gp ]     = $gp;
			$data['modules'][] = $m->info();
			if ($m->upgradable) {
				$upCount++;
			}
		}
		usort($data['modules'], ArrayCompare::compare('status', 'd'));
		$data['groups']  = $groups;
		$data['type']    = $type;
		$data['upCount'] = $upCount;

		return view($data);
	}

	public function stop($module) {
		$m = App::getModuleById($module);
		if ($m) {
			/**@var \wula\cms\CmfModule $m */
			if ($m->isKernel) {
				return Ajax::error('无法停用内核模块');
			}
			$m->stop();

			return Ajax::reload('document', '停用成功');
		} else {
			return Ajax::error('要停用的模块不存在');
		}
	}

	public function start($module) {
		$m = App::getModuleById($module);
		if ($m) {
			/**@var \wula\cms\CmfModule $m */
			$m->start();

			return Ajax::reload('document', '启用成功');
		} else {
			return Ajax::error('要启用的模块不存在');
		}
	}

	public function install($module) {
		$m = App::getModuleById($module);
		if ($m) {
			try {
				/**@var \wula\cms\CmfModule $m */
				if ($m->install(App::db())) {
					return Ajax::reload('document', '『' . $m->getName() . '』安装成功');
				}

				return Ajax::error('无法安装『' . $m->getName() . '』');
			} catch (\PDOException $e) {
				return Ajax::error($e->getMessage());
			}
		} else {
			return Ajax::error('要安装的模块不存在');
		}
	}

	public function uninstall($module) {
		$m = App::getModuleById($module);
		if ($m) {
			/**@var \wula\cms\CmfModule $m */
			if ($m->isKernel) {
				return Ajax::error('无法卸载内核模块');
			}
			try {
				/**@var \wula\cms\CmfModule $m */
				if ($m->uninstall()) {
					return Ajax::reload('document', '『' . $m->getName() . '』卸载成功');
				}

				return Ajax::error('无法卸载『' . $m->getName() . '』');
			} catch (\PDOException $e) {
				return Ajax::error($e->getMessage());
			}
		} else {
			return Ajax::error('要卸载的模块不存在');
		}
	}

	public function upgrade($module) {
		$m = App::getModuleById($module);
		if ($m) {
			try {
				/**@var \wula\cms\CmfModule $m */
				if ($m->upgrade(App::db(), $m->getCurrentVersion(), $m->installedVersion)) {
					return Ajax::reload('document', '『' . $m->getName() . '』升级成功');
				}

				return Ajax::error('无法升级『' . $m->getName() . '』');
			} catch (\PDOException $e) {
				return Ajax::error($e->getMessage());
			}
		} else {
			return Ajax::error('要升级的模块不存在');
		}
	}

	public function detail($module) {
		$m = App::getModuleById($module);
		if ($m) {
			$data['module']     = $m->info();
			$data['changelogs'] = array_reverse($m->getVersionList(), true);
			$path               = $m->getPath() . DS . 'README.md';
			if (is_file($path)) {
				$data['module']['doc'] = MarkdownExtra::defaultTransform(@file_get_contents($path));
			} else {
				$data['module']['doc'] = '此模块作者很懒，未提供任何文档，使用它全靠您蒙！';
			}

			return view($data);
		}

		return Ajax::fatal('模块不存在', 404);
	}
}