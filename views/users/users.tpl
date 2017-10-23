<section class="vbox wulaui" id="core-users-workset">
    <header class="bg-light dk header b-b clearfix">
        <div class="row m-t-sm">
            <div class="col-sm-6 m-b-xs">
                <a href="{'~core/users/edit'|app}" class="btn btn-sm btn-success edit-admin" data-ajax="dialog"
                   data-dialog-width="700px" data-dialog-id="dlg-admin-form" data-dialog-title="新的管理员"
                   data-dialog-type="green" data-dialog-icon="fa fa-user">
                    <i class="fa fa-plus"></i> 新管理员
                </a>
                <div class="btn-group">
                    <a href="{'~core/users/del'|app}" data-ajax data-grp="#core-admin-table tbody input.grp:checked"
                       data-confirm="你真的要删除这些用户吗？" data-warn="请选择要删除的用户" class="btn btn-danger btn-sm"><i
                                class="fa fa-trash"></i> 删除</a>
                    <a href="{'~core/users/set-status/0'|app}" data-ajax
                       data-grp="#core-admin-table tbody input.grp:checked" data-confirm="你真的要禁用这些用户吗？"
                       data-warn="请选择要禁用的用户" class="btn btn-sm btn-warning"><i class="fa fa-square-o"></i> 禁用</a>
                    <a href="{'~core/users/set-status/1'|app}" data-ajax
                       data-grp="#core-admin-table tbody input.grp:checked" data-confirm="你真的要激活这些用户吗？"
                       data-warn="请选择要激活的用户" class="btn btn-sm btn-primary"><i class="fa fa-check-square-o"></i>
                        激活</a>
                </div>
            </div>
            <div class="col-sm-6 m-b-xs text-right">
                <form data-table-form="#core-admin-table" class="form-inline">
                    <input type="hidden" id="admin-role-id" name="rid" value=""/>
                    <div class="checkbox m-l-xs m-r-xs">
                        <label>
                            <input type="checkbox" name="status" value="0" onchange="$('#btn-do-search').click()"/> 被禁用的
                        </label>
                    </div>
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="input-sm form-control" placeholder="{'Search'|t}"/>
                        <span class="input-group-btn">
                            <button class="btn btn-sm btn-info" id="btn-do-search" type="submit">Go!</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </header>
    <section class="w-f bg-white">
        <div class="table-responsive">
            <table id="core-admin-table" data-auto data-table="{"~core/users/data"|app}" data-sort="id,d"
                   style="min-width: 800px">
                <thead>
                <tr>
                    <th width="20">
                        <input type="checkbox" class="grp"/>
                    </th>
                    <th width="60" data-sort="id,d">ID</th>
                    <th width="100" data-sort="username,a">账户</th>
                    <th width="100" data-sort="nickname,a">姓名</th>
                    {'core.admin.table'|tablehead}
                    <th width="10" class="text-right">{'core.admin.table'|tableset:'#admin-grid'}</th>
                </tr>
                </thead>
            </table>
        </div>
    </section>
    <footer class="footer b-t">
        <div data-table-pager="#core-admin-table"></div>
    </footer>
</section>