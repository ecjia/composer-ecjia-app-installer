// JavaScript Document
;
(function(app, $) {
	app.install = {
		//初始化配置必填项验证
		start: function() {


			//验证数据库密码是否正确
			var params = "db_host=" + dbhost + "&"
				+ "db_port=" + dbport + "&"
				+ "db_user=" + dbuser + "&"
				+ "db_pass=" + dbpassword + "&"
				+ "dbdatabase=" + dbdatabase;
			var url = $('.check_db_correct').attr('data-url');

			var status = true;
			$.ajax({
				type: 'post',
				url: url,
				data: params,
				async: false,
				success: function(result) {
					if (result.state != 'success') {
						smoke.alert(result.message, {ok: js_lang.ok,});
						status = false;
						return false;
					}
				},
			});
			if (status == false) {
				return false;
			}

			//验证是否确认覆盖数据库
			if (is_create == 1) {
				var check_result;
				var url = $('.check_db_exists').attr('data-url');
				$.ajax({
					type: 'post',
					url: url,
					data: params,
					async: false,
					success: function(result) {
						check_result = result;
					},
				});
				if (check_result.is_exist == true) {
					smoke.confirm(js_lang.database_name_already_exists, function(e) {
						if (e) {
							status = true;
							$('input[name="is_create"]').val(0); //覆盖数据库
							start_install();
						} else {
							status = false;
							$('#dbdatabase').val(dbdatabase).focus();
							return false;
						}
					}, {
						ok: js_lang.ok,
						cancel: js_lang.cancel
					});
				} else {
					start_install();
				}
			} else {
				start_install();
			}
		},

		return_setting: function() {
			$('body').css('height', 'auto');
			$('#js-ecjia_deploy').css('display', 'block');
			$('#js-monitor').css('display', 'none');
			$('#js-monitor-notice').css('display', 'none');

			$('input[name="is_create"]').val(1);
			$('.path').children('li').removeClass('current').eq(2).addClass('current');
			$('#js-install-return-once').css('display', 'none');
		},

		check: function() {
			if ($('.configuration_system_btn').hasClass('disabled')) {
				smoke.alert(js_lang.unable_install, {ok: js_lang.ok,});
				return false;
			}
		},

        //验证配置信息必填项
        showmessage: function (id, msg) {
            $('.ui_showmessage').find('.close').parent().remove();
            $('.control-group').removeClass("error f_error");

            var html = $('<div class="staticalert alert alert-error ui_showmessage"><a class="close" data-dismiss="alert">×</a>' + msg + '</div>');
            $('#js-ecjia_deploy').before(html);

            $('#' + id).closest("li.control-group").addClass("error f_error");
            $('body,html').animate({
                scrollTop: 0
            }, 300);

            $('.close').on('click', function() {
                $('.ui_showmessage').find('.close').parent().remove();
            });
            window.setTimeout(function() {
                html.remove()
            }, 3000);
        },

        //开始安装
        start_install: function() {
            $('.ui_showmessage').find('.close').parent().remove();
            $('.control-group').removeClass("error f_error");
            $('body').scrollTop(0).css('height', '100%');
            $('#js-ecjia_deploy').css('display', 'none');
            $('.path').children('li').removeClass('current').eq(3).addClass('current');

            app.install.progress(0);
            app.install.install();
            $.cookie('install_step4', 1);
            return false;
        },

        //进度条控制
        progress: function(val) {
            let html;
            if (val === 100) {
                html = js_lang.installation_complete
            } else {
                html = val + '%';
            }
            let progress_bar_el = $('.progress-bar');
            progress_bar_el.css('width', val + '%');
            progress_bar_el.html(html);
        },

        //安装程序
        install: function() {
            $("#js-monitor").css('display', 'block');
            $('#js-monitor-notice').css('display', 'block');
            createConfigFile();
        },


        //创建配置文件
        createConfigFile: function() {
            var tzs = $("#js-timezones");
            var tz = tzs ? "timezone=" + tzs.val() : "";
            var params = "db_host=" + $("#dbhost").val() + "&"
                + "db_port=" + $("#dbport").val() + "&"
                + "db_user=" + $("#dbuser").val() + "&"
                + "db_pass=" + $("#dbpassword").val() + "&"
                + "db_name=" + $("#dbdatabase").val() + "&"
                + "db_prefix=" + $("#dbprefix").val() + "&" + tz;

            notice_html = '<div class="install_notice">'+ js_lang.create_configuration + '</div>';
            $('#js-notice').html(notice_html);

            var url = $('input[name="create_config_file"]').val();
            var is_create = $('input[name="is_create"]').val();
            $.post(url, params, function(result) {
                if (result.state === 'error') {
                    ErrorMsg(result.message);
                } else {
                    SuccessMsg();
                    progress(result.percent);
                    if (is_create === 1) {
                        createDatabase();
                    } else {
                        installStructure();
                    }
                }
            });
        }

	}

	var lf = "<br />";
	var notice = null;
	var notice_html = '';
	var correct_img = $('input[name="correct_img"]').val();
	var error_img = $('input[name="error_img"]').val();







	// 初始化数据库
	function createDatabase() {
		var params = "db_host=" + $("#dbhost").val() + "&"
			+ "db_port=" + $("#dbport").val() + "&"
			+ "db_user=" + $("#dbuser").val() + "&"
			+ "db_pass=" + $("#dbpassword").val() + "&"
			+ "db_name=" + $("#dbdatabase").val();

		notice_html += '<div class="install_notice">'+ js_lang.create_database + '</div>', $('#js-notice').html(notice_html);

		var url = $('input[name="create_database"]').val();
		$.post(url, params, function(result) {
			if (result.state == 'error') {
				ErrorMsg(result.message);
			} else {
				SuccessMsg();
				progress(result.percent);
				installStructure();
			}
		});
	}

	//提示程序安装终止信息
	function stopNotice() {
		$("#js-monitor-wait-please").html(js_lang.installation_abort);
	}

	//显示完成（成功）信息
	function SuccessMsg() {
		notice_html += "<span class='install_correct'><img src=" + correct_img + ">"+ js_lang.success + "</span>" + lf;
		$('#js-notice').html(notice_html);
	}

	//显示错误信息
	function ErrorMsg(result) {
		stopNotice();
		notice_html += "<span class='install_error'><img src=" + error_img + ">" + js_lang.fail + "</span>" + lf;
		$("#js-monitor-notice").css('display', "block");

		notice_html += "<strong class='m_l30' style='color:red'>"+ js_lang.prompt + result + "</strong>";
		$('#js-notice').html(notice_html);
		$('#js-install-return-once').css('display', 'block');
	}

	//安装数据库结构
	function installStructure() {
		notice_html += '<div class="install_notice">'+ js_lang.install_database_structure + '</div>';
		$('#js-notice').html(notice_html);

		var url = $('input[name="install_structure"]').val();
		$.post(url, '', function(result) {
			if (result.state == 'error') {
				ErrorMsg(result.message);
			} else {
				progress(result.percent);
				if (result.more > 0) {
					notice_text = "<span class='install_correct' id='numtips'>"+ js_lang.remainder + result.more + js_lang.piece + " ...</span>" ;
					$('#js-notice').append(notice_text);
					installStructureMore();
				} else {
					SuccessMsg();
					installBaseData();
				}
			}
		});
	}

	function installStructureMore() {
		var url = $('input[name="install_structure"]').val();
		$.post(url, '', function(result) {
			if (result.state == 'error') {
				ErrorMsg(result.message);
			} else {
				progress(result.percent);
				if (result.more > 0) {
					var notice_text = js_lang.remainder + result.more + js_lang.piece + " ..." ;
					$('#numtips').text(notice_text);
					installStructureMore();
				} else {
					$('#numtips').remove();
					SuccessMsg();
					installBaseData();
				}
			}
		});
	}

	//安装基础数据
	function installBaseData() {
		notice_html += '<div class="install_notice">' + js_lang.install_basic_data + '</div>';
		$('#js-notice').html(notice_html);

		var url = $('input[name="install_base_data"]').val();
		$.post(url, '', function(result) {
			if (result.state == 'error') {
				ErrorMsg(result.message);
			} else {
				progress(result.percent);
				SuccessMsg();
				if ($("input[name='js-install-demo']").attr("checked")) {
					installDemoData();
				} else {
					createAdminPassport();
				}

			}
		});
	}

	//安装演示数据
	function installDemoData() {
		notice_html += '<div class="install_notice">'+ js_lang.install_demo_data + '</div>';
		$('#js-notice').html(notice_html);

		var url = $('input[name="install_demo_data"]').val();
		$.post(url, '', function(result) {
			if (result.state == 'error') {
				ErrorMsg(result.message);
			} else {
				progress(result.percent);
				SuccessMsg();
				createAdminPassport();
			}
		});
	}

	//创建管理员帐号
	function createAdminPassport() {
		notice_html += '<div class="install_notice">'+ js_lang.create_administrator_account + '</div>';
		$('#js-notice').html(notice_html);

		var params = "admin_name=" + $("#username").val() + "&"
			+ "admin_password=" + $("#userpassword").val() + "&"
			+ "admin_password2=" + $("#confirmpassword").val() + "&"
			+ "admin_email=" + $("#usermail").val();

		var url = $('input[name="create_admin_passport"]').val();
		$.post(url, params, function(result) {
			if (result.state == 'error') {
				ErrorMsg(result.message);
			} else {
				progress(100);
				SuccessMsg();
				var url = $('input[name="done"]').val();
				window.setTimeout(function() {
					location.href = url;
				}, 1000);
			}
		});
	}





})(ecjia.front, jQuery);

//end