<div class="bg-light lter">
    <div class="wrapper-lg p-b-xs p-t-xs">
        <div class="row">
            <div class="col-md-3">
                <h6 class="m-b-xs">名称</h6>
                <p class="text-xxl m-b-none"><i class="fa fa-puzzle-piece text-warning"></i> {$module.name}</p>
                {if $module.home}
                    <p class="text-sm"><a href="{$module.home}" target="_blank" class="text-info">查看主页</a></p>
                {/if}
            </div>
            <div class="col-md-2">
                <h6 class="m-b-xs">当前版本</h6>
                <p class="text-xxl">{$module.ver}</p>
            </div>
            <div class="col-md-7">
                <h6 class="m-b-xs">作者</h6>
                <p class="text-xxl">{$module.author}</p>
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs p-t-n-xs">
        <li class="m-l-lg active"><a href="#module-{$module.namespace}-doc" data-toggle="tab">文档</a></li>
        <li class=""><a href="#module-{$module.namespace}-changelog" data-toggle="tab">修改记录</a></li>
    </ul>
</div>
<div class="p-t-xs">
    <div class="tab-content">
        <div class="tab-pane active" id="module-{$module.namespace}-doc">
            <div class="markdown-body">
                {$module.doc}
            </div>
        </div>
        <div class="tab-pane" id="module-{$module.namespace}-changelog">
            <div class="timeline" style="max-width: 600px;">
                {foreach $changelogs as $vver => $log}
                    <div class="timeline-item {if $log@iteration%2==0}alt{/if}">
                        <div class="timeline-caption">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <span class="arrow {if $log@iteration%2==0}right{else}left{/if}"></span>
                                    <span class="timeline-icon"><i class="fa fa-code time-icon bg-dark"></i></span>
                                    <span class="timeline-date">{$vver}</span>
                                    <p>{$log}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
                <div class="timeline-footer">
                    <a href="javascript:;"><i class="fa fa-plus time-icon inline-block bg-dark"></i></a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
		requirejs(['highlight'], function () {
			$('#module-{$module.namespace}-doc .markdown-body pre code').each(function (i, code) {
				hljs.highlightBlock(code);
			})
		});
    </script>
</div>