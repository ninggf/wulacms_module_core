<tbody data-total="{$total}" class="wulaui">
{foreach $items as $row}
    <tr>
        <td>
            <input type="checkbox" value="{$row.id}" class="grp"/>
        </td>
        <td>{$row.id}</td>
        <td>{$row.username}</td>
        <td>{$row.nickname}</td>
        {'core.admin.table'|tablerow:$row}
        <td class="text-right">
            <a href="{'~core/users/edit'|app}/{$row.id}" data-ajax="dialog" data-dialog-width="700px"
               data-dialog-id="dlg-admin-form" data-dialog-title="编辑『{$row.username|escape}』" data-dialog-type="orange"
               data-dialog-icon="fa fa-user" class="btn btn-xs edit-admin"> <i
                        class="fa fa-pencil-square-o text-primary"></i>
            </a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="{'core.admin.table'|tablespan:5}" class="text-center">无用户</td>
    </tr>
{/foreach}
</tbody>