<!DOCTYPE html>
<html class="bg-dark">
<head>
    <meta charset="UTF-8">
    <title>{'Install'|t} - {'wulacms'|t:$version}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="{'wula/ui/css/ui.css'|vendor}">
    {combinate type='js' ver='1.0'}
        <script src="{'wula/ui/js/jquery.min.js'|vendor}"></script>
        <script src="{'wula/ui/js/bootstrap.min.js'|vendor}"></script>
        <script src="{'wula/ui/js/common.min.js'|vendor}"></script>
        <script src="{'wula/ui/js/dialog/notify.min.js'|vendor}"></script>
        <script src="{'wula/ui/js/dialog/dialog.min.js'|vendor}"></script>
        <script src="{'wula/ui/js/fuelux/fuelux.min.js'|vendor}"></script>
        <script src="{'wula/ui/js/parsley/parsley.min.js'|vendor}"></script>
        <script src="{'wula/ui/js/app.js'|vendor:'min'}"></script>
    {'wula/ui/lang'|vendor|i18n}
    {/combinate}
</head>
<body>
<section id="content" class="m-t-md wrapper-md m-b-xs animated fadeInDown p-b-xs">
    <div class="container aside-xxl" style="min-width: 800px">
        <a href="." class="navbar-brand block">WulaCMS安装程序<sub>{$version}</sub></a>
        <section class="panel panel-default m-t-md bg-white wizard" id="install-wizard">
            <div class="wizard-steps clearfix" id="form-wizard">
                <ul class="steps">
                    <li data-target="#step1" class="active"><span class="badge badge-info">1</span>欢迎</li>
                    <li data-target="#step2"><span class="badge">2</span>环境检测</li>
                    <li data-target="#step3"><span class="badge">3</span>基础配置</li>
                    <li data-target="#step4"><span class="badge">4</span>配置数据库</li>
                    <li data-target="#step5"><span class="badge">5</span>安装</li>
                </ul>
            </div>
            <div class="step-content clearfix">
                <form action="{'core/install/setup'|app}" class="m-b-sm m-t-sm" method="post" id="install-form">
                    <div class="step-pane active scrollable" style="max-height: 350px" id="step1">
                        <div class="markdown-body" style="padding: 5px 0 25px 0;">
                            {$license}
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="check" data-required="true" data-error-message="请同意本协议"/>
                                我同意
                            </label>
                        </div>
                    </div>
                    <div class="step-pane scrollable" style="max-height: 350px" id="step2">
                        <div class="form-group clearfix">
                            <div class="col-xs-8">
                                <div class="checkbox h4">检测项</div>
                            </div>
                            <div class="col-xs-2">
                                <div class="checkbox h4">所需配置</div>
                            </div>
                            <div class="col-xs-2">
                                <div class="checkbox h4">实际配置</div>
                            </div>
                        </div>
                        {foreach $checked as $key=> $check}
                            <div class="form-group clearfix {if !$check.pass && $check.optional}has-warning{elseif !$check.pass}has-error{/if}">
                                <div class="col-xs-8">
                                    <div class="checkbox m-b-n">
                                        <label>
                                            <input onclick="return false;" type="checkbox" name="check_{$check@index}"
                                                   {if $check.pass}checked="checked"{/if}
                                                    {if !$check.optional}data-required="true" data-error-message="此项检测结果不满足要求" {/if}/> {$key}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="checkbox m-b-n">{$check.required}</div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="checkbox m-b-n">
                                        <span class="label label-{if $check.pass}success{elseif $check.optional}warning{else}danger{/if}">{$check.checked}</span>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                    <div class="step-pane scrollable" style="max-height: 350px" id="step3">
                        <p class="text-muted">&nbsp;&nbsp;（<b class="text-danger">*</b>）为必填项</p>
                        <div class="form-group clearfix">
                            <div class="col-xs-6">
                                <label for="name">网站名称（<b class="text-danger">*</b>）</label>
                                <input type="text" name="name" class="form-control" data-required="true"
                                       data-error-message="请填写网站名称"/>
                            </div>
                            <div class="col-xs-6">
                                <label for="name">部署环境</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" name="app_mode" value="pro" checked> 正式
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="app_mode" value="dev"> 开发
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-xs-6">
                                <label for="">网站管理域名</label>
                                <input type="text" name="domain" class="form-control"/>
                                <div class="note">指定后只能通过此域名登录后台</div>
                            </div>
                            <div class="col-xs-6">
                                <label for="">网站管理路径</label>
                                <input type="text" name="dashboard" class="form-control" placeholder="backend"/>
                                <div class="note">指定后只能通过此路径访问后台</div>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-xs-6">
                                <label for="">管理员账号（<b class="text-danger">*</b>）</label>
                                <input type="text" name="admin" class="form-control" data-required="true"
                                       data-error-message="请填写管理员账号"/>
                            </div>
                            <div class="col-xs-6">
                                <label for="pwd">管理员密码（<b class="text-danger">*</b>）</label>
                                <input type="password" name="pwd" id="pwd" class="form-control" data-required="true"
                                       data-parsley-minlength="6" data-error-message="请填写登录密码"/>
                            </div>
                        </div>
                    </div>
                    <div class="step-pane scrollable" style="max-height: 350px" id="step4">
                        <p class="text-muted">&nbsp;&nbsp;（<b class="text-danger">*</b>）为必填项</p>
                        <div class="form-group clearfix">
                            <div class="col-xs-6">
                                <label for="">数据库IP（<b class="text-danger">*</b>）</label>
                                <input type="text" name="dbhost" placeholder="localhost" class="form-control"
                                       data-required="true" data-error-message="请填写数据库IP"/>
                            </div>
                            <div class="col-xs-6">
                                <label for="">数据库端口</label>
                                <input type="number" name="dbport" class="form-control" value="3306"
                                       placeholder="3306"/>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-xs-6">
                                <label for="">数据库用户（<b class="text-danger">*</b>）</label>
                                <input type="text" name="dbuser" placeholder="root" class="form-control"
                                       data-required="true" data-error-message="请填写连接数据库的用户名"/>
                            </div>
                            <div class="col-xs-6">
                                <label for="">数据库密码（<b class="text-danger">*</b>）</label>
                                <input type="password" name="dbpwd" class="form-control" data-required="true"
                                       data-error-message="请填写连接数据库的密码"/>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-xs-6">
                                <label for="">数据库名称（<b class="text-danger">*</b>）</label>
                                <input type="text" name="dbname" placeholder="wulacms_db" class="form-control"
                                       data-required="true" data-error-message="请填写数据库名"/>
                            </div>
                            <div class="col-xs-6">
                                <label for="">编码</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" name="dbcharset" value="UTF8" checked> UTF8
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="dbcharset" value="UTF8MB4"> UTF8MB4
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="step-pane" id="step5">
                        <h3>正在安装，请不要刷新当前页面</h3>
                        <div class="scrollable m-b-sm" style="height: 320px">
                            <ul class="list-group" id="install-progress">
                                <li class="list-group-item" id="p-step1">
                                    <div class="clear">检测数据库连接</div>
                                    <div class="pull-right m-t-n">
                                        <i class="fa fa-refresh fa-spin"></i>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="progress m-b-none progress-striped  active">
                            <div class="progress-bar progress-bar-success" id="install-progress-bar"
                                 style="width: 5%"></div>
                        </div>
                    </div>
                </form>
                <div class="actions pull-right">
                    <button type="button" class="btn btn-default btn-sm btn-prev" disabled="disabled">上一步</button>
                    <button type="button" class="btn btn-default btn-sm btn-next" data-last="下一步">下一步</button>
                </div>
            </div>
        </section>
    </div>
</section>
<footer id="footer">
    <div class="text-center padder clearfix">
        <p>
            <small>{'wulacms'|t:$version}<br/>&copy; 2017</small>
        </p>
    </div>
</footer>
<script type="text/javascript">
	var tpl        = '<li class="list-group-item dy"><div class="clear"></div><div class="pull-right m-t-n"><i class="fa fa-refresh fa-spin"></i></div></li>';
	var insWizard  = $('#install-wizard').on('changed', function (e) {
		var data = insWizard.wizard('selectedItem');
		if (data.step == 4) {
			insWizard.wizard('setButtonText', {
				text: '立即安装'
			});
		} else if (data.step == 5) {
			insWizard.wizard('enable', false);
			var data = $('#install-form').serializeArray();
			$.post($('#install-form').attr('action'), data, 'json').done(function (data) {
				setupResp(data);
			}).fail(function (data) {
				alert(data.responseText);
			});
		}
	});
	var setupResp  = function (data) {
		var step = $('#p-step' + data.step);
		if (data.success) {
			step.find('i.fa').removeClass('fa-spin fa-refresh').addClass('fa-check').parent().addClass('text-success');
			$('#install-progress-bar').width(data.progress);
			setupNext(data);
		} else {
			setupError(data);
		}
	};
	var setupNext  = function (data) {
		if (!data.next) {
			if (data.text) {
				$('#p-step' + data.step).find('.clear').html(data.text);
			}
			return;
		}
		var id = 'p-step' + data.next + (data.m ? '-' + data.m : '');
		$('#' + id).remove();
		var s = $(tpl);
		s.attr('id', id).find('.clear').html(data.text);
		$('#install-progress').prepend(s);
		var param  = data.params || {
			step: data.next
		};
		param.step = data.next;
		$.post($('#install-form').attr('action'), param, 'json').done(function (data) {
			setupResp(data);
		}).fail(function (data) {
			alert(data.responseText);
		});
	};
	var setupError = function (data) {
		var step = $('#p-step' + data.step);
		step.find('i.fa').removeClass('fa-spin fa-refresh').addClass('fa-times').parent().addClass('text-danger');
		var text = step.find('.clear');
		text.html(text.html() + '&nbsp;&nbsp;[<span class="text-danger">' + data.msg + '</span>]');
	};
</script>
</body>
</html>