<?php
/*
 * wulacmf 安装控制器,此控制器只在wulacmf未安装的情况下可用。
 *
 * 此控制器通过检测conf/install.lock文件是否存在来决定是否启用安装器。
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace core\controllers;

use wulaphp\io\Response;
use wulaphp\mvc\controller\Controller;
use wulaphp\mvc\controller\SessionSupport;

class InstallController extends Controller {
	use SessionSupport;

	public function beforeRun($action, $refMethod) {
		// 只有conf/install.lock文件不存在时才可以进行安装操作.
		if (WULACMF_INSTALLED) {
			Response::respond(404);
		}
	}

	public function index() {
		if (is_file(CONFIG_PATH . 'license.html')) {
			$license = @file_get_contents(CONFIG_PATH . 'license.html');
		} else {
			$license = "条款";
		}

		return view(['license' => $license]);
	}
}