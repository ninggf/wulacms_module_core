<div id="modules-app">
    <wula-console-title>
        已安装模块
    </wula-console-title>
    <wula-ajax-table :table-data="modules" :is-group="true" :is-ajax="false" :none-border="true" url="{$table->url}" @on-row-click="onRowClick">
        <template slot="search">
            <Form-item prop="group" label="分组" :label-width="40">
                <i-select v-model="modules.forms.group">
                    <i-option v-for="gp in groups" value="gp.id">{{ gp.text }}</i-option>
                </i-select>
            </Form-item>
            <Form-item prop="name" label="名称" :label-width="80">
                <i-input type="text" v-model="modules.forms.name" placeholder="名称"></i-input>
            </Form-item>
        </template>
        <div slot="actions">aaa</div>
    </wula-ajax-table>
</div>
<script>
	 new Vue({
		el     : '#modules-app',
		data   : {
			groups:{$groups},
			modules: {
				columns: [
                    {$table}
				],
                forms:{
					group:"",
					name:""
                }
			}
		},
        methods:{
			'onRowClick': function(row){
                if(row.detail){
                   window.location.href = row.detail;
                }
            }
        }
	});
</script>