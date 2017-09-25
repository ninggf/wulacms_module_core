<div class="hbox stretch wulaui">
    <section>
        <div class="vbox">
            <header class="header clearfix bg-light dk b-b b-light">
                <ul class="nav nav-tabs no-radius" id="module-tabs">
                    {if $type == 'installed'}
                        <li class="active m-l-lg"><a href="#module-list" class="text-primary"
                                                     data-toggle="tab">已安装（{$modules|count}）</a></li>
                    {elseif $type == 'upgradable'}
                        <li class="active m-l-lg"><a href="#module-list" data-toggle="tab">可升级（{$modules|count}）</a>
                        </li>
                    {else}
                        <li class="active m-l-lg"><a href="#module-list" data-toggle="tab">未安装（{$modules|count}）</a>
                        </li>
                    {/if}
                    <li class="hidden" id="module-detail-tab">
                        <a href="#module-detail" data-toggle="tab">模块详情</a>
                    </li>
                </ul>
            </header>
            <section class="scrollable bg-white-only">
                <div class="tab-content">
                    <div class="tab-pane active" id="module-list">
                        <div class="table-responsive">
                            <table id="core-module-table" data-table style="min-width: 600px">
                                <thead>
                                <tr>
                                    <th width="100">名称</th>
                                    <th>描述</th>
                                    <th width="120">版本</th>
                                    <th width="150">作者</th>
                                    <th width="60"></th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $modules as $m}
                                    <tr data-field-gp="{$m.group}" class="{if $m.status == 0}text-muted{/if}">
                                        <td><a href="javascript:;" rel="{$m.namespace}"
                                               class="module-name"><b>{$m.name}</b></a>
                                        </td>
                                        <td>{$m.desc|escape}</td>
                                        {if $type=='installed' || $type == 'upgradable'}
                                            <td>{$m.cver}{if $m.upgradable}
                                                    <b class="text-primary">&#10148;</b>
                                                    {$m.ver}{/if}
                                            </td>
                                        {else}
                                            <td>{$m.ver}</td>
                                        {/if}
                                        <td>{$m.author}</td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                {if $m.status == -1}
                                                    <a href="{'~core/module/install/'|app}{$m.namespace}" data-ajax
                                                       data-confirm="你真要安装该模块吗?" class="btn btn-xs btn-primary"
                                                       title="点击安装"><i class="fa fa-hdd-o"></i></a>
                                                {elseif $m.status == 1}
                                                    <a href="{'~core/module/stop/'|app}{$m.namespace}" data-ajax
                                                       data-confirm="你真的要停用模块『{$m.name}』吗?"
                                                       class="btn btn-xs btn-warning" title="点击停用"><i
                                                                class="fa fa-pause"></i></a>
                                                    <a href="{'~core/module/uninstall/'|app}{$m.namespace}" data-ajax
                                                       data-confirm="你真的要卸载模块『{$m.name}』吗?"
                                                       class="btn btn-xs btn-danger" title="点击卸载"><i
                                                                class="fa fa-trash-o"></i></a>
                                                {elseif $m.status==2}
                                                    <a href="{'~core/module/upgrade/'|app}{$m.namespace}" data-ajax
                                                       data-confirm="你确定要升级此模块吗?" class="btn btn-xs btn-primary"
                                                       title="点击升级"><i class="fa fa-arrow-up"></i></a>
                                                {else}
                                                    <a href="{'~core/module/start/'|app}{$m.namespace}" data-ajax
                                                       data-confirm="你确定要启用模块『{$m.name}』吗?"
                                                       class="btn btn-xs btn-success" title="点击启用"><i
                                                                class="fa fa-play"></i></a>
                                                    <a href="{'~core/module/uninstall/'|app}{$m.namespace}" data-ajax
                                                       data-confirm="你真的要卸载模块『{$m.name}』吗?"
                                                       class="btn btn-xs btn-danger" title="点击卸载"><i
                                                                class="fa fa-trash-o"></i></a>
                                                {/if}
                                            </div>
                                        </td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="module-detail" data-load data-lazy>

                    </div>
                </div>
            </section>
        </div>
    </section>
    <aside class="hidden-xs aside-sm b-l bg-white-only">
        <div class="vbox">
            <header class="bg-light dk header b-b">
                <p>模块分组 ({$groups|count})</p>
            </header>
            <section class="hidden-xs scrollable m-t-xs">
                <ul class="nav nav-pills nav-stacked no-radius" id="core-module-groups">
                    <li class="active">
                        <a href="javascript:;"> 全部 </a>
                    </li>
                    {foreach $groups as $gp}
                        <li>
                            <a href="javascript:;" rel="{$gp}"> {$gp}</a>
                        </li>
                    {/foreach}
                </ul>
            </section>
        </div>
    </aside>
    <script type="text/javascript">
		var group = $('#core-module-groups');
		group.find('a').click(function () {
			var me = $(this), mp = me.closest('li');
			if (mp.hasClass('active')) {
				return;
			}
			group.find('li').not(mp).removeClass('active');
			mp.addClass('active');
			$('#core-module-table').wulatable('filter', 'gp', me.attr('rel'));
			return false;
		});
		$('#core-module-table').on('click', 'a.module-name', function () {
			$('#module-detail-tab').removeClass('hidden').find('a').click();
			$('#module-detail').data('load', '{"~core/module/detail/"|app}' + $(this).attr('rel')).reload();
		});
		$('#module-tabs').find('li.m-l-lg a').click(function () {
			$('#module-detail-tab').addClass('hidden');
		});
    </script>
</div>