<?php
namespace core;

use wulaphp\app\App;
use wulaphp\app\Module;

class CoreModule extends Module {
	public function getName() {
		return '内核';
	}

	public function getDescription() {
		return 'wulacms内容管理框架内核模块';
	}

	public function getHomePageURL() {
		return 'https://www.wulacms.com/modules/core';
	}
}

App::register(new CoreModule());