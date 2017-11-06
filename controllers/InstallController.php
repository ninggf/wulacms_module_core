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

use Michelf\MarkdownExtra;
use wula\cms\CmfModule;
use wulaphp\app\App;
use wulaphp\auth\Passport;
use wulaphp\io\Response;
use wulaphp\mvc\controller\Controller;
use wulaphp\mvc\controller\SessionSupport;

class InstallController extends Controller {
	use SessionSupport;

	public function beforeRun($action, $refMethod) {
		// 只有conf/install.lock文件不存在时才可以进行安装操作.
		if (WULACMF_INSTALLED || !defined('WULACMF_WEB_INSTALLER')) {
			Response::respond(404);
		}
	}

	public function index() {
		if (is_file(CONFIG_PATH . 'license.md')) {
			$license = MarkdownExtra::defaultTransform(@file_get_contents(CONFIG_PATH . 'license.md'));
		} else {
			$license = $license = MarkdownExtra::defaultTransform(@file_get_contents($this->module->getPath('license.md')));
		}

		$checked['安全模式']        = CmfModule::checkEnv('safe_mode', 0);
		$checked['文件上传']        = CmfModule::checkEnv('file_uploads', 1);
		$checked['输出缓冲区']       = CmfModule::checkEnv('output_buffering', 0, true);
		$checked['自动开启SESSION'] = CmfModule::checkEnv('session.auto_start', 0);

		$checked ['PHP版本'] = [
			'required' => '5.6.0+',
			'checked'  => phpversion(),
			'pass'     => version_compare('5.6.0', phpversion(), '<=')
		];
		$pass              = extension_loaded('pdo');
		if ($pass) {
			$drivers = \PDO::getAvailableDrivers();
			if (empty ($drivers)) {
				$pass = false;
			} else {
				$pass = in_array('mysql', $drivers);
			}
		}
		$checked ['PDO (mysql)'] = [
			'required' => '有',
			'checked'  => $pass ? '有' : '无',
			'pass'     => $pass,
			'optional' => false
		];
		$pass                    = extension_loaded('gd');
		$checked ['GD']          = [
			'required' => '有',
			'checked'  => $pass ? '有' : '无',
			'pass'     => $pass,
			'optional' => false
		];
		$pass                    = extension_loaded('json');
		$checked ['JSON']        = [
			'required' => '有',
			'checked'  => $pass ? '有' : '无',
			'pass'     => $pass,
			'optional' => false
		];

		$pass                  = extension_loaded('mbstring');
		$checked ['MB_String'] = [
			'required' => '有',
			'checked'  => $pass ? '有' : '无',
			'pass'     => $pass,
			'optional' => false
		];
		$pass                  = extension_loaded('curl') && version_compare('7.0.0', curl_version()['version'], '<=');
		$checked ['curl']      = [
			'required' => '7.0.0+',
			'checked'  => $pass ? curl_version()['version'] : '无',
			'pass'     => $pass,
			'optional' => false
		];
		$pass                  = extension_loaded('apcu');
		$checked ['apcu']      = [
			'required' => '可选',
			'checked'  => $pass ? '有' : '无',
			'pass'     => $pass,
			'optional' => true
		];

		$pass              = extension_loaded('redis');
		$checked ['redis'] = ['required' => '可选', 'checked' => $pass ? '有' : '无', 'pass' => $pass, 'optional' => true];

		$pass                  = extension_loaded('memcached');
		$checked ['memcached'] = [
			'required' => '可选',
			'checked'  => $pass ? '有' : '无',
			'pass'     => $pass,
			'optional' => true
		];

		$pass             = extension_loaded('scws');
		$checked ['scws'] = ['required' => '可选', 'checked' => $pass ? '有' : '无', 'pass' => $pass, 'optional' => true];

		$f                = TMP_PATH;
		$checked['tmp目录'] = CmfModule::checkFile($f);

		$f                 = LOGS_PATH;
		$checked['logs目录'] = CmfModule::checkFile($f);

		$f                   = CONFIG_PATH;
		$checked['conf目录']   = CmfModule::checkFile($f);
		$_SESSION['started'] = 1;

		return view(['license' => $license, 'version' => '1.0.0', 'checked' => $checked]);
	}

	public function setupPost($step = '1') {
		set_time_limit(300);
		$data = ['success' => false, 'step' => $step];
		if (!$_SESSION['started']) {
			$data['msg'] = 'SESSION未开启，无法安装';

			return $data;
		}
		switch ($step) {
			case '1':
				$cfg   = rqsts([
					'name',
					'app_mode',
					'domain',
					'dashboard' => 'backend',
					'admin',
					'pwd'
				]);
				$dbcfg = rqsts([
					'dbhost' => 'localhost',
					'dbport' => 3306,
					'dbuser',
					'dbpwd',
					'dbname',
					'dbcharset'
				], true, [
					'dbhost'    => 'host',
					'dbport'    => 'port',
					'dbuser'    => 'user',
					'dbpwd'     => 'password',
					'dbcharset' => 'encoding'
				]);
				if (!$cfg['dashboard']) {
					$cfg['dashboard'] = 'backend';
				}
				$_SESSION['install_cfg']   = $cfg;
				$_SESSION['install_dbcfg'] = $dbcfg;
				unset($dbcfg['dbname']);
				try {
					$db = App::db($dbcfg);
					if ($db == null) {
						throw new \Exception('无法连接数据库');
					}
					$data['success']  = true;
					$data['next']     = 2;
					$data['text']     = '创建数据库';
					$data['progress'] = '10%';
				} catch (\Exception $e) {
					$data['msg'] = $e->getMessage();
				}
				break;
			case '2'://创建数据库
				$dbcfg  = $_SESSION['install_dbcfg'];
				$dbname = $dbcfg['dbname'];
				unset($dbcfg['dbname']);
				try {
					$db      = App::db($dbcfg);
					$dialect = $db->getDialect();
					$dbs     = $dialect->listDatabases();
					$rst     = in_array($dbname, $dbs);
					if (!$rst) {
						$rst = $dialect->createDatabase($dbname, $dbcfg['encoding']);
					}
					if (!$rst) {
						throw_exception('Cannot create the database ' . $dbname);
					}
					$data['success']  = true;
					$data['next']     = 3;
					$data['progress'] = '20%';
					$siteConfig       = @include CONFIG_PATH . 'install_config.php';
					$modules          = ['core', 'dashboard', 'media', 'site', 'model', 'page'];
					if (isset($siteConfig['modules'])) {
						$modules = array_merge($modules, (array)$siteConfig['modules']);
					}
					$ms = [];
					foreach ($modules as $i => $m) {
						$module = App::getModuleById($m);
						if ($module) {
							$ms[] = [$m, $module->getName()];
						}
					}
					if ($ms) {
						$data['text']           = '安装『' . $ms[0][1] . '』';
						$data['m']              = $ms[0][0];
						$data['params']         = ['m' => $data['m'], 'idx' => 0];
						$_SESSION['install_ms'] = $ms;
					} else {
						$data['success'] = false;
						$data['msg']     = '无可用模块';
					}
				} catch (\Exception $e) {
					$data['msg'] = $e->getMessage();
				}
				break;
			case '3'://安装
				$m            = rqst('m');
				$idx          = irqst('idx');
				$data['step'] = $step . '-' . $m;
				try {
					$module = App::getModuleById($m);
					$dbcfg  = $_SESSION['install_dbcfg'];
					if (!$module) {
						throw_exception('模块未定义');
					}
					if (!$module->install(App::db($dbcfg), 1)) {
						throw_exception('安装失败');
					}

					$ms = $_SESSION['install_ms'];
					$idx++;
					$len = count($ms);
					if ($idx >= $len) {
						$data['next']     = 4;
						$data['text']     = '创建管理员';
						$data['progress'] = '90%';
					} else {
						$data['next']     = 3;
						$data['text']     = '安装『' . $ms[ $idx ][1] . '』';
						$data['m']        = $ms[ $idx ][0];
						$data['params']   = ['m' => $data['m'], 'idx' => $idx];
						$data['progress'] = (20 + floor(70 * $idx / $len)) . '%';
					}
					$data['success'] = true;
				} catch (\Exception $e) {
					$data['msg'] = $e->getMessage();
				}
				break;
			case '4'://创建管理员
				$cfg      = $_SESSION['install_cfg'];
				$username = $cfg['admin'];
				$password = $cfg['pwd'];

				$data['next']     = 5;
				$data['text']     = '最后一点点工作';
				$data['progress'] = '95%';
				$user['id']       = 1;
				$user['username'] = $username;
				$user['nickname'] = '网站所有者';
				$user['hash']     = Passport::passwd($password);
				try {
					$dbcfg = $_SESSION['install_dbcfg'];
					$db    = App::db($dbcfg);
					$db->insert($user)->into('user')->exec();

					$db->insert([
						['user_id' => 1, 'role_id' => 1],
						['user_id' => 1, 'role_id' => 2]
					], true)->into('{user_role}')->exec();
					$data['success'] = true;
				} catch (\Exception $e) {
					$data['msg'] = $e->getMessage();
				}
				break;
			case '5'://保存配置文件
				try {
					$cfg       = $_SESSION['install_cfg'];
					$domain    = $cfg['domain'];
					$dashboard = $cfg['dashboard'];
					$name      = $cfg['name'];
					$app_mode  = $cfg['app_mode'];
					$dbcfg     = $_SESSION['install_dbcfg'];
					$cfg       = CONFIG_PATH . 'install_config.php';
					if (is_file($cfg)) {
						$dbconfig         = file_get_contents($cfg);
						$r['{dashboard}'] = $dashboard;
						$r['{domain}']    = $domain;
						$r['{name}']      = str_replace("'", "\\'", $name);
						$dbconfig         = str_replace(array_keys($r), array_values($r), $dbconfig);
						if (!@file_put_contents(CONFIG_PATH . 'config.php', $dbconfig)) {
							throw_exception('无法保存配置文件:' . CONF_DIR . '/config.php');
						}
					}
					$dbconfig           = @file_get_contents(APPROOT . VENDOR_DIR . '/wula/cms-support/tpl/dbconfig.php');
					$r['{db.host}']     = $dbcfg['host'];
					$r['{db.port}']     = $dbcfg['port'];
					$r['{db.name}']     = $dbcfg['dbname'];
					$r['{db.charset}']  = $dbcfg['encoding'];
					$r['{db.user}']     = $dbcfg['user'];
					$r['{db.password}'] = $dbcfg['password'];
					$dbconfig           = str_replace(array_keys($r), array_values($r), $dbconfig);
					if (!@file_put_contents(CONFIG_PATH . 'dbconfig.php', $dbconfig)) {
						throw_exception('无法保存数据库配置文件:' . CONF_DIR . '/dbconfig.php');
					}
					if ($app_mode == 'dev') {
						$dcf[] = '[app]';
						$dcf[] = 'debug = DEBUG_DEBUG';
						$dcf[] = 'dashboard = ' . $dashboard;
						$dcf[] = 'domain = ' . $domain;
						$dcf[] = '';
						$dcf[] = '[db]';
						$dcf[] = 'db.host = ' . $dbcfg['host'];
						$dcf[] = 'db.port = ' . $dbcfg['port'];
						$dcf[] = 'db.name = ' . $dbcfg['dbname'];
						$dcf[] = 'db.user = ' . $dbcfg['user'];
						$dcf[] = 'db.password = ' . $dbcfg['password'];
						$dcf[] = 'db.charset = ' . $dbcfg['encoding'];
						if (!@file_put_contents(CONFIG_PATH . '.env', implode("\n", $dcf))) {
							throw_exception('无法保存配置到' . CONFIG_PATH . '.env');
						}
					}
					$data['next']     = 0;
					$data['progress'] = '100%';
					if (@file_put_contents(CONFIG_PATH . 'install.lock', time())) {
						$data['success'] = true;
						$url             = WWWROOT_DIR . ($dashboard ? $dashboard : 'backend');
						$data['text']    = '恭喜！安装完成啦，立即访问<a href="' . $url . '">后台</a>爽一把。';
					} else {
						$data['msg'] = '无法保存锁定文件';
					}
				} catch (\Exception $e) {
					$data['msg'] = $e->getMessage();
				}
				break;
			default:
				$data['msg'] = '未知安装步骤';
		}
		if (!$data['success'] && $data['step'] > 2) {
			@unlink(CONFIG_PATH . 'dbconfig.php');
			@unlink(CONFIG_PATH . 'config.php');
			@unlink(CONFIG_PATH . '.env');
		}

		return $data;
	}
}