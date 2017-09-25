<section class="hbox stretch" id="core-account-workset">
    <aside class="aside aside-md b-r">
        <section class="vbox">
            <header class="header bg-light dk b-b">
                <button class="btn btn-icon btn-default btn-sm pull-right visible-xs m-r-xs" data-toggle="class:show"
                        data-target="#core-role-wrap">
                    <i class="fa fa-reorder"></i>
                </button>
                <p class="h4">角色</p>
            </header>
            <section class="hidden-xs scrollable w-f m-t-xs" id="core-role-wrap">
                <div id="core-role-list" class="slim-scroll wulaui" data-height="100%" data-disable-fade-out="true"
                     data-distance="0" data-size="5px" data-color="#333333" data-load="{'~core/users/roles'|app}"
                     data-loading="#core-role-list">
                    {include './roles.tpl'}
                </div>
            </section>
            <footer class="footer b-t hidden-xs">
                <a class="btn btn-success btn-sm pull-right edit-role" data-ajax="dialog" href="{'~core/role/edit'|app}"
                   data-dialog-type="green" data-dialog-width="400px" data-dialog-title="新的角色"
                   data-dialog-icon="fa fa-users" data-dialog-id="dlg-role-form">
                    <i class="fa fa-plus"></i> 新角色
                </a>
            </footer>
        </section>
    </aside>

    <section>
        <section class="hbox stretch">
            <aside class="aside" id="admin-grid" data-load="{'~core/users/users'|app}">
                {include './users.tpl'}
            </aside>
            <aside class="aside hidden" id="acl-space"></aside>
        </section>
    </section>
    <script type="text/javascript">
		$('#core-account-workset').on('build.dialog', '.edit-admin', function (e) {
			e.buttons = {
				ok    : {
					text    : '保存',
					btnClass: 'btn-green',
					action  : function () {
						$('#core-admin-form').data('ajaxDone', 'close:dlg-admin-form').submit();
						return false;
					}
				},
				cancel: {
					text  : '取消',
					action: function () {
						if ($('#core-admin-form').data('ajaxSending')) {
							return false;
						}
					}
				}
			};
		}).on('build.dialog', '.edit-role', function (e) {
			e.buttons = {
				ok    : {
					text    : '保存',
					btnClass: 'btn-green',
					action  : function () {
						$('#core-role-form').data('ajaxDone', 'close:dlg-role-form').submit();
						return false;
					}
				},
				cancel: {
					text  : '取消',
					action: function () {
						if ($('#core-role-form').data('ajaxSending')) {
							return false;
						}
					}
				}
			};
		}).on('click', 'a.role-li', function () {
			var me = $(this), mp = me.closest('li'), rid = mp.data('rid'), group = me.closest('ul');
			if (mp.hasClass('active')) {
				return;
			}
			group.find('li').not(mp).removeClass('active');
			mp.addClass('active');
			$('#admin-role-id').val(rid ? rid : '');
			$('[data-table-form="#core-admin-table"]').submit();
			return false;
		})
    </script>
</section>

