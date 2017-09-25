<div class="row wulaui">
    <div class="col-sm-9">
        <form id="core-admin-form" name="AdminForm" data-validate="{$rules|escape}" action="{'~core/users/save'|app}"
              data-ajax data-ajax-done="reload:#core-admin-table" method="post">
            <input type="hidden" name="id" id="id" value="{$id}"/>
            {$form|render}
        </form>
    </div>
    <div class="col-sm-3">
        avatar here
    </div>
</div>