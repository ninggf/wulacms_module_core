<div class="wulaui">
    <form id="core-role-form" name="RoleForm" data-validate="{$rules|escape}" action="{'~core/role/save'|app}" data-ajax
          data-ajax-done="reload:#core-role-table" method="post">
        <input type="hidden" name="id" id="id" value="{$id}"/>
        {$form|render}
    </form>
</div>