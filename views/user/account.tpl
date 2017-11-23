<section class="vbox">
    <section class="scrollable bg-white-only">
        <ul class="nav nav-tabs p-t bg-light dk">
            <li class="active m-l-lg"><a href="#my-account-pane" data-toggle="tab">账户</a></li>
            <li><a href="#my-pwd-pane" data-toggle="tab">密码</a></li>
        </ul>
        <div class="wrapper-lg" style="max-width: 980px;">
            <div class="tab-content">
                <div class="tab-pane active" id="my-account-pane">
                    <div class="hbox">
                        <aside>
                            <form name="UserTable" action="{'~core/user/account'|app}" data-validate="{$rules|escape}"
                                  data-ajax method="post" role="form">
                                <input type="hidden" name="id" value="{$uid}"/>
                                {$form|render}
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">确定修改</button>
                                    <button type="reset" class="btn btn-default">重置</button>
                                </div>
                            </form>
                        </aside>
                        <aside class="aside-md">
                            <div class="row">
                                <div class="col-xs-12 m-l-lg">
                                    <label>头像</label>
                                    <div data-uploader="{'~core/user/update-avatar'|app}/{$uid}" class="m-l-lg"
                                         id="user-avatar" data-width="120" data-name="avatar" data-auto
                                         data-value="{$avatar}" data-max-file-size="512K" data-resize="250,,70,1"></div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
                <div class="tab-pane" id="my-pwd-pane">
                    <form name="ChPwdForm" action="{'~core/user/chpwd'|app}" data-validate="{$pwdrules|escape}"
                          data-ajax method="post" role="form">
                        <input type="hidden" name="id" value="{$uid}"/>
                        {$pwdform|render}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">确定修改</button>
                            <button type="reset" class="btn btn-default">重置</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
		$('#user-avatar').on('uploader.remove', function () {
			if (confirm('你确定要删除当前头像吗？')) {
				$.get("{'~core/user/del-avatar'|app}").done(function () {
					$('#my-avatar').attr('src', wulassets('avatar.jpg'));
				});
			} else {
				return false;
			}
		}).on('uploader.uploaded', function (e, file) {
			$('#my-avatar').attr('src', wulamedia(file.url));
		});
    </script>
</section>
