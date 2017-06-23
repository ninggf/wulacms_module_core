<div id="module-detail">
    <div class="control-breadcrumb" v-once>
        <ul>
            <li><a :href="back"><Icon type="arrow-return-left" size="22"></Icon></a></li>
            <li>模块详情</li>
        </ul>
    </div>
    <div v-once class="scoreboard">
        <div data-control="toolbar">
            <div class="scoreboard-item title-value">
                <h4>插件</h4>
                <p class="fa fa-cubes">{{ name }}</p>
                <p class="description">
                    <a :href="home" target="_blank">
                        查看主页 </a>
                </p>
            </div>
            <div class="scoreboard-item title-value">
                <h4>当前版本</h4>
                <p>{{ installedVersion }}</p>
                <p class="description" v-if="upgradable">
                    <a>可升级到 {{ ver }} </a>
                </p>
            </div>
            <div class="scoreboard-item title-value">
                <h4>作者</h4>
                <p>{{ author }}</p>
            </div>
        </div>
    </div>
    <div class="m-lr-n15" v-once>
        <Tabs :animated="false" :value="ctab">
            {if $docHtml}
            <Tab-pane label="文档" icon="ios-book">
                <div class="markdown-body">
                    {$docHtml}
                </div>
            </Tab-pane>
            {/if}
            <Tab-pane label="更新日志" icon="network">
                <div class="changelog">
                    <dl>
                        <template v-for="(vls,vl) in vers">
                            <template v-if="typeof vls === 'object'">
                                <dt>{{ vl }}</dt>
                                <dd v-for="vv in vls">{{ vv }}</dd>
                            </template>
                            <template v-else>
                                <dt>{{ vl }}</dt>
                                <dd>{{ vls }}</dd>
                            </template>
                        </template>
                    </dl>
                </div>
            </Tab-pane>
            {if $license}
                <Tab-pane label="LICENSE" icon="android-document">
                    <div style="font-size: 14px;">
                        {$license|nl2br}
                    </div>
                </Tab-pane>
            {/if}
            <Tab-pane label="接口" icon="social-javascript" v-if="hasApi">
                <div>

                </div>
            </Tab-pane>
            <div slot="extra" class="toolbar">
                <Button-group size="small">
                    <wula-ajax-button :confirm="`你确定要安装'${name}'模块吗?`" v-if="!installed" type="primary" :url="upgrade">
                        安装
                    </wula-ajax-button>

                    <wula-ajax-button :confirm="`你确定将'${name}'模块从${installedVersion}升级到${ver}吗?`" v-if="upgradable"
                                      type="info" :url="upgrade">升级
                    </wula-ajax-button>

                    <wula-ajax-button :confirm="`你确定要禁用'${name}'模块吗?`" v-if="installed && enabled" type="ghost"
                                      :url="upgrade">禁用
                    </wula-ajax-button>
                    <wula-ajax-button :confirm="`你确定要启用'${name}'模块吗?`" v-if="installed && !enabled" type="success"
                                      :url="upgrade">启用
                    </wula-ajax-button>

                    <wula-ajax-button :confirm="`你确定要卸载'${name}'模块吗?`" v-if="installed" type="error" :url="upgrade">卸载
                    </wula-ajax-button>
                </Button-group>
            </div>
        </Tabs>
    </div>
</div>

<script>
	new Vue({
		el      : '#module-detail',
		mixins  : [Wula.mixins],
		data    : {$module},
		computed: {
			back   : function () {
				return this.url('~core/module/' + this.ops, 1);
			},
			upgrade: function () {
				return this.url('~core/module/upgrade/' + this.namespace)
			}
		}
	});
</script>