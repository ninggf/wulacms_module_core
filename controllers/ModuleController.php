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
use wula\form\UiGroupTable;
use wulaphp\app\App;

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
			$modules[] = $gp;
		}
		$uitable[] = ['key' => 'name', 'title' => '名称', 'sortable' => true, 'width' => 180];
		$uitable[] = ['key' => 'desc', 'title' => '描述'];
		$uitable[] = ['key' => 'ver', 'title' => '版本', 'width' => 100];
		$uitable[] = ['key' => 'author', 'title' => '作者', 'width' => 120];

		return mustache(['table' => $uitable, 'groups' => json_encode(array_unique($modules))]);
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
			$info['detail'] = App::hash('~core/module/detail/' . $info['namespace']);
			$modules[]      = $info;
		}

		$data                    = UiGroupTable::by($modules, 'group', 'name', count($modules) * 100);
		$data->permits['delete'] = $this->passport->cando('d:system/module');

		return $data;
	}

	public function detail($id, $type = '') {

		return mustache();
	}
}