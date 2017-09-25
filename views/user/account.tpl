<section class="vbox">
    <section class="scrollable bg-white wrapper">
        <ul class="nav nav-tabs pull-in">
            <li class="active m-l-lg"><a href="#my-account-pane" data-toggle="tab">账户</a></li>
            <li><a href="#my-pwd-pane" data-toggle="tab">密码</a></li>
        </ul>
        <div class="wrapper-lg">
            <div class="tab-content">
                <div class="tab-pane active" id="my-account-pane">
                    <div class="hbox">
                        <aside>
                            <form name="UserTable" action="{'~core/user/account'|app}" data-validate="{$rules|escape}"
                                  data-ajax method="post" role="form">
                                <input type="hidden" name="id" value="{$uid}"/>
                                {$form|render}
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">确定修改</button>
                                    <button type="reset" class="btn btn-default">重置</button>
                                </div>
                            </form>
                        </aside>
                        <aside class="aside-md">

                        </aside>
                    </div>
                </div>
                <div class="tab-pane" id="my-pwd-pane">
                    <form name="ChPwdForm" action="{'~core/user/chpwd'|app}" data-validate="{$pwdrules|escape}"
                          data-ajax method="post" role="form">
                        <input type="hidden" name="id" value="{$uid}"/>
                        {$pwdform|render}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">确定修改</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</section>
