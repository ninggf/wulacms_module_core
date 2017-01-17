<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>wulacms installer v1.0</title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-default/index.css">
    <style>
        #installer {
            max-width: 900px;
            margin: 50px auto;
        }

        .clearfix:before,
        .clearfix:after {
            display: table;
            content: "";
        }

        .clearfix:after {
            clear: both
        }

        .bottom {
            margin-top: 13px;
            margin-bottom: -13px;
            line-height: 12px;
            text-align: right;
        }

        .slide-left-enter, .slide-right-leave-active {
            opacity: 0;
            -webkit-transform: translate(30px, 0);
            transform: translate(30px, 0);
        }

        .slide-left-leave-active, .slide-right-enter {
            opacity: 0;
            -webkit-transform: translate(-30px, 0);
            transform: translate(-30px, 0);
        }
    </style>
    <script type="text/javascript" src="{'zepto.min.js'|assets}"></script>
    <script type="text/javascript" src="https://unpkg.com/vue/dist/vue.js"></script>
    <script type="text/javascript" src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
    <script type="text/javascript" src="https://unpkg.com/element-ui/lib/index.js"></script>
</head>
<body>
<div id="installer">
    <div id="app">
        <el-row :gutter="20" v-loading.fullscreen.lock="fullscreenLoading">
            <el-col :span="4">
                <el-steps :space="60" :active="active" direction="vertical" process-status="finish"
                          finish-status="success">
                    <el-step title="安装许可" icon="information"></el-step>
                    <el-step title="环境检测" icon="view"></el-step>
                    <el-step title="数据库配置" icon="setting"></el-step>
                    <el-step title="创建管理员" icon="edit"></el-step>
                    <el-step title="安装" icon="upload"></el-step>
                    <el-step title="完成" icon="circle-check"></el-step>
                </el-steps>
            </el-col>
            <el-col :span="20">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span style="line-height: 36px;">WULACMS安装程序</span>
                    </div>
                    <div>
                        <transition :name="transitionName">
                            <router-view></router-view>
                        </transition>
                    </div>
                    <div class="bottom clearfix">
                        <el-button v-show="prevBtnShow" @click="prev" type="warning" icon="arrow-left">{{ prevText }}
                        </el-button>
                        <el-button v-show="checkBtnShow" type="success" @click="check">重新检测</el-button>
                        <el-button v-show="nextBtnShow" type="primary" @click="next">{{ nextText }}<i
                                    class="el-icon-arrow-right"></i></el-button>
                    </div>
                </el-card>
            </el-col>
        </el-row>
    </div>
</div>

<script type="text/javascript">

	const welcome = Vue.component('wulaWelcome', function (resolve) {
		resolve({
			template: '<div>welcome</div>'
		});
	});

	const check   = Vue.component('wulaCheck', function (resolve) {
		resolve({
			template: '<div>check</div>'
		})
	});
	const db      = Vue.component('wulaDB', function (resolve) {
		resolve({
			template: '<div>database</div>'
		})
	});
	const sa      = Vue.component('wulaSA', function (resolve) {
		resolve({
			template: '<div>check</div>'
		})
	});
	const install = Vue.component('wulaInstall', function (resolve) {
		resolve({
			template: '<div>install</div>'
		})
	});
	const done    = Vue.component('wulaDone', function (resolve) {
		resolve({
			template: '<div>done</div>'
		})
	});

	const rules   = [
		{
			path: '/', component: welcome
		},
		{
			path: '/check', component: check
		},
		{
			path: '/db', component: db
		},
		{
			path: '/sa', component: sa
		},
		{
			path: '/install', component: install
		},
		{
			path: '/done', component: done
		}
	];

	var getHash  = function (next) {
		var hash = window.location.hash.replace('#', '');
		var j    = next ? 1 : -1;
		for (var i in rules) {
			if (rules[i].path == hash) {
				j += parseInt(i, 10);
				if (j > 5) {
					return [5, rules[5].path, next];
				} else if (j < 0) {
					return [0, rules[0].path, next];
				} else {
					return [j, rules[j].path, next];
				}
			}
		}
		return false;
	};
	var checkBtn = function (setp) {
		this.nextBtnShow  = setp != 5 && setp != 4;
		this.prevBtnShow  = setp != 0 && setp != 5 && setp != 4;
		this.checkBtnShow = setp == 1;
		if (setp == 3) {
			this.nextText = '立即安装';
		} else if (setp == 4) {
			this.nextBtnShow = false;
			this.prevBtnShow = false;
		} else if (setp == 5) {
			this.nextText = '完成';
		} else {
			this.nextText = '下一步';
		}
	};

	const router = new VueRouter({
		mode  : 'hash',
		routes: rules
	});

	const app = new Vue({
		router : router,
		data   : function () {
			var cActive = getHash(true);
			cActive     = cActive ? cActive[0] - 1 : 0;
			return {
				fullscreenLoading: false,
				prevText         : '上一步',
				nextText         : cActive == 3 ? '立即安装' : (cActive == 5 ? '完成' : '下一步'),
				prevBtnShow      : cActive != 0 && cActive != 5 && cActive != 4,
				nextBtnShow      : cActive != 5 && cActive != 4,
				checkBtnShow     : cActive == 1,
				active           : cActive,
				transitionName   : 'slide-left'
			}
		},
		methods: {
			prev : function () {
				var path = getHash(false);
				if (path) {
					var setp    = path[0];
					var url     = path[1];
					this.active = setp;
					checkBtn.apply(this, [setp]);
					this.transitionName = 'slide-right';
					router.push(url);
				} else {
					this.prevBtnShow = false;
				}
			},
			next : function () {
				var path = getHash(true);
				if (path) {
					var me      = this;
					var setp    = path[0];
					var url     = path[1];
					this.active = setp;
					checkBtn.apply(this, [setp]);
					this.transitionName = 'slide-left';
					router.push(url);
					if (setp == 4) {
						setTimeout(function () {
							router.push('/done');
							me.active = 6;
						}, 5000);
					}
				} else {
					this.nextBtnShow = false;
				}
			},
			check: function () {

			}
		}
	}).$mount('#app');

</script>
</body>
</html>