<div id="change-password">
    <wula-console-title>
        修改密码
    </wula-console-title>
    <Row>
        <i-col span="6" :xs="24" :sm="12" :lg="6">
            <wula-ajax-form :label-width="80"  :form-data="user" url="{'~core/user/change-password'|app}">
                <form-item label="原密码" prop="old" :required="true" :rules="{ required: true }">
                    <i-input v-model="user.forms.old" placeholder="请输入密码"></i-input>
                </form-item>
                <form-item label="新密码" prop="new_password" :required="true" :rules="{ required: true,message: '请输入新密码' }">
                    <i-input v-model="user.forms.new_password" placeholder="请输入密码"></i-input>
                </form-item>
                <form-item label="确认密码" prop="confirm_password" :required="true" :rules="{ required: true }">
                    <i-input v-model="user.forms.confirm_password" placeholder="请输入密码"></i-input>
                </form-item>
            </wula-ajax-form>
        </i-col>
    </Row>

</div>
<script>
    new Vue({
        el:'#change-password',
        data:{
			user:{
				forms:{
					old:'',
					new_password:'',
					confirm_password:''
				},
                rules:{
					old:{
						required: true, message: '请上传图片'
					}
				}
			}
		}
	})
</script>