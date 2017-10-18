<div class="row wulaui">
    <div class="col-sm-9">
        <form id="core-admin-form" name="AdminForm" data-validate="{$rules|escape}" action="{'~core/users/save'|app}"
              data-ajax data-ajax-done="reload:#core-admin-table" method="post">
            <input type="hidden" name="id" id="id" value="{$id}"/>
            {$form|render}
        </form>
    </div>
    <div class="col-sm-3">
        <label>头像</label>
        <div data-uploader="{'~core/users/update-avatar'|app}/{$id}" id="user-avatar" data-width="120"
             data-name="avatar" data-auto data-value="{$avatar}" data-max-file-size="512K"
             data-resize="250,,70,1"></div>
    </div>
    <script type="text/javascript">
		$('#user-avatar').on('uploader.remove', function () {
			if (confirm('你真的要删除当前头像吗?')) {
				$.get("{'~core/users/del-avatar'|app}/{$id}")
			} else {
				return false;
			}
		});
    </script>
</div>