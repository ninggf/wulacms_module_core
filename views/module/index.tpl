<div id="modules-app">
    <wula-console-title>
        {{ title }}
    </wula-console-title>
    <wula-ajax-table :table-data="modules" :is-group="true" :is-ajax="false" :none-border="true" url="{$table->url}"
                     @on-row-click="onRowClick">
        <template slot="search">
            <Form-item prop="group" label="分组" :label-width="40">
                <i-select v-model="modules.forms.group">
                    <i-option v-for="gp in groups" :key="gp.id" value="gp.id">{{ gp.text }}</i-option>
                </i-select>
            </Form-item>
            <Form-item prop="name" label="名称" :label-width="80">
                <i-input type="text" v-model="modules.forms.name" placeholder="名称"></i-input>
            </Form-item>
        </template>
    </wula-ajax-table>
</div>
<script>
	var tableData = {
		type   : '{$type}',
		title  : '{$title}',
		groups : {$groups},
		modules: {
			columns: [
                {$table}
			],
			forms  : {
				group: "",
				name : ""
			}
		}
	};
	new Vue({
		el     : '#modules-app',
		data   : tableData,
		mixins : [Wula.mixins],
		methods: {
			'onRowClick': function (row) {
				if (row.namespace) {
					window.location.href = this.url('~core/module/detail/' + row.namespace+'/'+this.type, 1);
				}
			}
		}
	});
</script>