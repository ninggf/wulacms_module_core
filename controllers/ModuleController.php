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
use wula\ui\UiGroupTable;
use wulaphp\app\App;
use wulaphp\io\Ajax;

/**
 * Class ModuleController
 * @package core\controllers
 * @acl     m:system/module
 */
class ModuleController extends BackendController {
	public function index($type = 'installed') {
		$uitable = new UiGroupTable('~core/module/data/' . $type);
		$modules = [];
		foreach (App::modules($type) as $m) {
			$gp        = $m->group;
			$modules[] = ['id' => $gp, 'text' => $gp];
		}
		$title     = $type == 'installed' ? '已安装模块' : ($type == 'upgradable' ? '可升级模块' : '未安装模块');
		$uitable[] = ['key' => 'name', 'title' => '名称', 'sortable' => true, 'width' => 180];
		$uitable[] = ['key' => 'desc', 'title' => '描述'];
		$uitable[] = ['key' => 'ver', 'title' => '版本', 'width' => 100];
		$uitable[] = ['key' => 'author', 'title' => '作者', 'width' => 120];

		return mustache(['title' => $title, 'type' => $type, 'table' => $uitable, 'groups' => json_encode(array_unique($modules))]);
	}

	public function data($type = 'installed', $group = '', $name = '') {
		$modules = [];
		foreach (App::modules($type) as $m) {
			$info = $m->info();
			if ($group && !$info['group'] == $group) {
				continue;
			}
			if ($name && strpos($info['name'], $name) === false) {
				continue;
			}
			$modules[] = $info;
		}
		$data                    = UiGroupTable::by($modules, 'group', 'name', count($modules));
		$data->permits['delete'] = $this->passport->cando('d:system/module');

		return $data;
	}

	public function detail($id, $type = '') {
		$module = App::getModule($id);
		if ($module) {
			$data = $module->info();
		} else {
			return Ajax::error('模块不存在');
		}
		$dir     = $module->getPath();
		$license = @file_get_contents($dir . '/LICENSE');
		$doc     = @file_get_contents($dir . '/README.MD');
		if ($doc) {
			$docHtml = MarkdownExtra::defaultTransform($doc);
		} else {
			$docHtml = '';
		}
		$data['vers'] = array_reverse($module->getVersionList(), true);

		$data['ops']    = $type;
		$data['hasApi'] = is_dir($dir . '/apis/');
		$data['ctab']   = 0;

		return mustache(['module' => json_encode($data), 'license' => $license, 'docHtml' => $docHtml]);
	}
}