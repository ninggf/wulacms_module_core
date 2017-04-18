<div id="installed-modules">
    <wula-console-title>
        已安装模块
    </wula-console-title>
    <wula-ajax-table :table-data="modules" url="{$table->url}">
        <div slot="actions">aaa</div>
    </wula-ajax-table>
</div>
<script>
	new Vue({
		el  : '#installed-modules',
		data: {
			modules: {
				columns: [
                    {$table},
					{
						key   : 'actions',
						width : 120,
						title : '',
						render: function (row, col) {
							return `{$actions}`;
						}
					}
				]
			}
		}
	});
</script>