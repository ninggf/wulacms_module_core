<div class="vbox wulaui">
    <header class="header bg-light lter b-b clearfix">
        <p class="h4">角色授权: {$role.name}</p>
    </header>
    <section class="scrollable w-f bg-white">
        <form action="{'~core/role/set-acl'|app}" method="post" id="acl-form" data-ajax>
            <input type="hidden" name="role_id" value="{$role.id}"/>
            <div class="row">
                <div class="col-lg-8 col-md-10 col-xs-12">
                    <table data-table="{'~core/role/acldata'|app}/{$role.id}" data-tree="true"
                           data-leaf-icon="fa fa-gear m-r-xs" data-folder-icon1="fa fa-folder-open-o m-r-xs"
                           data-folder-icon2="fa fa-folder m-r-xs">
                        <thead>
                        <tr>
                            <th></th>
                            <th width="60" class="text-center text-success">允许</th>
                            <th width="60" class="text-center">继承</th>
                            <th width="60" class="text-center text-danger">禁用</th>
                        </tr>
                        </thead>
                        {include './acldata.tpl'}
                    </table>
                </div>
            </div>
        </form>
    </section>
    <footer class="footer b-t bg-light lter">
        <button class="btn btn-sm btn-primary" id="acl-save">授权</button>
        <button class="btn btn-sm btn-success" id="acl-save-c">授权&关闭</button>
        <button class="btn btn-sm btn-warning" id="acl-reset">重置权限</button>
        <button class="btn btn-sm btn-default" id="acl-cancel">取消</button>
    </footer>
    <script type="text/javascript">
		$('#acl-cancel').click(function () {
			$('#acl-space').addClass('hidden').hide();
			$('#admin-grid').removeClass('hidden').show();
		});
		$('#acl-save').click(function () {
			$('#acl-form').submit();
		});
		$('#acl-save-c').click(function () {
			$('#acl-form').data('ajaxDone', 'hide:#acl-space;show:#admin-grid').submit();
		});
		$('#acl-reset').click(function () {
			$('#acl-form').get(0).reset();
		});
    </script>
</div>