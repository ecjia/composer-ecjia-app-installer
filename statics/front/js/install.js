// JavaScript Document
;
(function(app, $) {
	app.install = {
		// //初始化配置必填项验证
		// start: function() {
        //
        //
		// 	//验证数据库密码是否正确
		// 	var params = "db_host=" + dbhost + "&"
		// 		+ "db_port=" + dbport + "&"
		// 		+ "db_user=" + dbuser + "&"
		// 		+ "db_pass=" + dbpassword + "&"
		// 		+ "dbdatabase=" + dbdatabase;
		// 	var url = $('.check_db_correct').attr('data-url');
        //
		// 	var status = true;
		// 	$.ajax({
		// 		type: 'post',
		// 		url: url,
		// 		data: params,
		// 		async: false,
		// 		success: function(result) {
		// 			if (result.state != 'success') {
		// 				smoke.alert(result.message, {ok: js_lang.ok,});
		// 				status = false;
		// 				return false;
		// 			}
		// 		},
		// 	});
		// 	if (status == false) {
		// 		return false;
		// 	}
        //
		// 	//验证是否确认覆盖数据库
		// 	if (is_create == 1) {
		// 		var check_result;
		// 		var url = $('.check_db_exists').attr('data-url');
		// 		$.ajax({
		// 			type: 'post',
		// 			url: url,
		// 			data: params,
		// 			async: false,
		// 			success: function(result) {
		// 				check_result = result;
		// 			},
		// 		});
		// 		if (check_result.is_exist == true) {
		// 			smoke.confirm(js_lang.database_name_already_exists, function(e) {
		// 				if (e) {
		// 					status = true;
		// 					$('input[name="is_create"]').val(0); //覆盖数据库
		// 					start_install();
		// 				} else {
		// 					status = false;
		// 					$('#dbdatabase').val(dbdatabase).focus();
		// 					return false;
		// 				}
		// 			}, {
		// 				ok: js_lang.ok,
		// 				cancel: js_lang.cancel
		// 			});
		// 		} else {
		// 			start_install();
		// 		}
		// 	} else {
		// 		start_install();
		// 	}
		// },

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

        // //开始安装
        // start_install: function() {
        //     $('.ui_showmessage').find('.close').parent().remove();
        //     $('.control-group').removeClass("error f_error");
        //     $('body').scrollTop(0).css('height', '100%');
        //     $('#js-ecjia_deploy').css('display', 'none');
        //     $('.path').children('li').removeClass('current').eq(3).addClass('current');
        //
        //     app.install.progress(0);
        //     app.install.install();
        //     $.cookie('install_step4', 1);
        //     return false;
        // },

        // //进度条控制
        // progress: function(val) {
        //     let html;
        //     if (val === 100) {
        //         html = js_lang.installation_complete
        //     } else {
        //         html = val + '%';
        //     }
        //     let progress_bar_el = $('.progress-bar');
        //     progress_bar_el.css('width', val + '%');
        //     progress_bar_el.html(html);
        // },

        //安装程序
        // install: function() {
        //     $("#js-monitor").css('display', 'block');
        //     $('#js-monitor-notice').css('display', 'block');
        //     createConfigFile();
        // },

        //提示程序安装终止信息
        stopNotice: function() {
            $("#js-monitor-wait-please").html(js_lang.installation_abort);
        },

        //显示完成（成功）信息
        // SuccessMsg: function() {
        //     notice_html += "<span class='install_correct'><img alt='' src=" + correct_img + " />"+ js_lang.success + "</span>" + lf;
        //     $('#js-notice').html(notice_html);
        // },

        //显示错误信息
        // ErrorMsg: function(result) {
        //     stopNotice();
        //     notice_html += "<span class='install_error'><img alt='' src=" + error_img + " />" + js_lang.fail + "</span>" + lf;
        //     $("#js-monitor-notice").css('display', "block");
        //
        //     notice_html += "<strong class='m_l30' style='color:red'>"+ js_lang.prompt + result + "</strong>";
        //     $('#js-notice').html(notice_html);
        //     $('#js-install-return-once').css('display', 'block');
        // }

	}

	//消息提示
	app.notice = {
	    notice_html: '',

	    success_notice_template: function (status) {
            let correct_img = $('input[name="correct_img"]').val();
            return "<span class='install_correct'><img alt='' src='" + correct_img + "' />"+ status + "</span><br/>";
        },

        error_notice_template: function (status, msg) {
            let error_img = $('input[name="error_img"]').val();
            return "<span class='install_error'><img alt='' src='" + error_img + "' />" + status + "</span><br/>" +
                "<strong class='m_l30 ecjia-color-red'>"+ msg + "</strong>";
        },

        install_notice_template: function (msg) {
	        return '<div class="install_notice">'+ msg + '</div>';
        },

        open: function () {
            $("#js-monitor").css('display', 'block');
            $('#js-monitor-notice').css('display', 'block');
        },

	    show: function (html) {
            app.notice.notice_html += html;
            $('#js-notice').html(app.notice.notice_html);
        },

        stop: function () {

        },

        success: function (html) {
            app.notice.notice_html += html;
            $('#js-notice').html(app.notice.notice_html);
        },

        error: function (html) {
            app.notice.notice_html += html;
            $("#js-monitor-notice").css('display', "block");
            $('#js-notice').html(app.notice.notice_html);
            $('#js-install-return-once').css('display', 'block');
        },

        addSubject: function (text) {
            app.notice.show(app.notice.install_notice_template(text));
        },

        addErrorMessage: function (msg) {
            app.notice.error(app.notice.error_notice_template(js_lang.fail, msg));
        },

        addSuccessMessage: function () {
            app.notice.success(app.notice.success_notice_template(js_lang.success));
        },

        addNumTips: function (num) {
            let text = sprintf(js_lang.remainder, num);
            let html = "<span class='install_correct' id='numtips'>"+ text + "</span>";
            $('#js-notice').append(html);
        },

        updateNumTips: function (num) {
            let text = sprintf(js_lang.remainder, num);
            $('#numtips').html(text);
        },

        removeNumTips: function () {
            $('#numtips').remove();
        }

    }

	//安装进度条
	app.progress_bar = {
        //开启
	    reset: function () {
	        let val = 0;
            let html = val + '%';
            let progress_bar_el = $('.progress-bar');
            progress_bar_el.css('width', val + '%');
            progress_bar_el.html(html);
        },

        complete: function () {
            let val = 100;
            let html = val + '%';
            let progress_bar_el = $('.progress-bar');
            progress_bar_el.css('width', val + '%');
            progress_bar_el.html(html);
        },

        update: function (val) {
            let html = val + '%';
            let progress_bar_el = $('.progress-bar');
            progress_bar_el.css('width', val + '%');
            progress_bar_el.html(html);
        }
    }

    //安装任务
	app.task = {
        //验证数据库密码是否正确
        checkDatabasePasswordCorrectTask: function (next) {
            console.log('checkDatabasePasswordCorrectTask');

            let params = {
                db_host: $("#db_host").val(),
                db_port: $("#db_port").val(),
                db_user: $("#db_user").val(),
                db_pass: $("#db_password").val(),
                db_database: $('#db_database').val(),
                db_prefix: $('#db_prefix').val(),
                timezone: $('#timezone').val()
            };

            let url = $('.check_db_correct').attr('data-url');
            $.ajax({
                type: 'post',
                url: url,
                data: params,
                async: false,
                success: function(result) {
                    if (result.state !== 'success') {
                        smoke.alert(result.message, {ok: js_lang.ok,});
                        return false;
                    }
                    else {
                        next();
                    }
                }
            });

        },

        //验证是否确认覆盖数据库
        checkDatabaseExistsTask: function (next) {
            console.log('checkDatabaseExistsTask');

            let is_create = $('input[name="is_create"]').val();

            //验证是否确认覆盖数据库
            if (parseInt(is_create) === 1) {

                let params = {
                    db_host: $("#db_host").val(),
                    db_port: $("#db_port").val(),
                    db_user: $("#db_user").val(),
                    db_pass: $("#db_password").val(),
                    db_database: $('#db_database').val(),
                    db_prefix: $('#db_prefix').val(),
                    timezone: $('#timezone').val()
                };

                // var check_result;
                let url = $('.check_db_exists').attr('data-url');
                $.ajax({
                    type: 'post',
                    url: url,
                    data: params,
                    async: false,
                    success: function(result) {
                        // check_result = result;

                        if (result.db_is_exist === 1) {
                            smoke.confirm(js_lang.database_name_already_exists, function(event) {
                                if (event) {
                                    // status = true;
                                    $('input[name="is_create"]').val(0); //覆盖数据库
                                    // start_install();
                                    next();
                                } else {
                                    // status = false;
                                    $('#db_database').val(params.db_database).focus();
                                    return false;
                                }
                            }, {
                                ok: js_lang.ok,
                                cancel: js_lang.cancel
                            });
                        } else {
                            next();
                        }
                    },
                });
            } else {
                next();
            }
        },

        //安装程序启动
	    installStartTask: function (next) {
            console.log('installStartTask');

            $('.ui_showmessage').find('.close').parent().remove();
            $('.control-group').removeClass("error f_error");
            $('body').scrollTop(0).css('height', '100%');
            $('#js-ecjia_deploy').css('display', 'none');
            $('.path').children('li').removeClass('current').eq(3).addClass('current');

	        app.progress_bar.reset();
            app.notice.open();

            next();
        },

        //创建配置文件
        createConfigFileTask: function(next) {
            console.log('createConfigFileTask');
            let params = {
                db_host: $("#db_host").val(),
                db_port: $("#db_port").val(),
                db_user: $("#db_user").val(),
                db_pass: $("#db_password").val(),
                db_database: $('#db_database').val(),
                db_prefix: $('#db_prefix').val(),
                timezone: $('#timezone').val()
            };

            app.notice.addSubject(js_lang.create_configuration)

            let url = $('input[name="create_config_file_action"]').val();
            $.post(url, params, function(result) {
                if (result.state === 'error') {
                    app.notice.addErrorMessage(result.message);
                } else {
                    app.notice.addSuccessMessage();
                    app.progress_bar.update(result.percent);
                    next();
                }
            });
        },

        // 初始化数据库
        createDatabaseTask: function(next) {
            console.log('createDatabaseTask');

            let is_create = $('input[name="is_create"]').val();
            console.log('is_create');
            console.log(is_create);
            if (parseInt(is_create) === 1) {
                // createDatabase();

                app.notice.addSubject(js_lang.create_database)

                let params = {
                    db_host: $("#db_host").val(),
                    db_port: $("#db_port").val(),
                    db_user: $("#db_user").val(),
                    db_pass: $("#db_password").val(),
                    db_database: $('#db_database').val(),
                    db_prefix: $('#db_prefix').val(),
                    timezone: $('#timezone').val()
                };

                let url = $('input[name="create_database_action"]').val();
                $.post(url, params, function(result) {
                    if (result.state === 'error') {
                        // ErrorMsg(result.message);
                        app.notice.addErrorMessage(result.message);
                    } else {
                        // SuccessMsg();
                        app.notice.addSuccessMessage();
                        app.progress_bar.update(result.percent);
                        // progress(result.percent);
                        // installStructure();
                        next();
                    }
                });

            } else {
                next();
            }

            // var params = "db_host=" + $("#dbhost").val() + "&"
            //     + "db_port=" + $("#dbport").val() + "&"
            //     + "db_user=" + $("#dbuser").val() + "&"
            //     + "db_pass=" + $("#dbpassword").val() + "&"
            //     + "db_name=" + $("#dbdatabase").val();



            // notice_html += '<div class="install_notice">'+ js_lang.create_database + '</div>';
            //     $('#js-notice').html(notice_html);


        },

        //安装数据库结构
        installStructureTask: function(next) {
            console.log('installStructureTask');

            // notice_html += '<div class="install_notice">'+ js_lang.install_database_structure + '</div>';
            // $('#js-notice').html(notice_html);

            app.notice.addSubject(js_lang.install_database_structure)

            let params = {
                db_host: $("#db_host").val(),
                db_port: $("#db_port").val(),
                db_user: $("#db_user").val(),
                db_pass: $("#db_password").val(),
                db_database: $('#db_database').val(),
                db_prefix: $('#db_prefix').val(),
                timezone: $('#timezone').val()
            };

            let url = $('input[name="install_structure_action"]').val();
            $.post(url, params, function(result) {
                if (result.state === 'error') {
                    app.notice.addErrorMessage(result.message);
                    // ErrorMsg(result.message);
                } else {
                    app.progress_bar.update(result.percent);
                    // progress(result.percent);
                    if (result.more > 0) {
                        // sprintf(js_lang.remainder, result.more);
                        // notice_text = "<span class='install_correct' id='numtips'>"+ js_lang.remainder + result.more + js_lang.piece + " ...</span>" ;
                        // $('#js-notice').append(notice_text);
                        app.notice.addNumTips(result.more);
                        app.task.installStructureSubTask(next);
                    } else {
                        // SuccessMsg();
                        app.notice.addSuccessMessage();
                        // installBaseData();
                        next();
                    }
                }
            });
        },

        installStructureSubTask: function(next) {
            console.log('installStructureSubTask');

            let params = {
                db_host: $("#db_host").val(),
                db_port: $("#db_port").val(),
                db_user: $("#db_user").val(),
                db_pass: $("#db_password").val(),
                db_database: $('#db_database').val(),
                db_prefix: $('#db_prefix').val(),
                timezone: $('#timezone').val()
            };

            let url = $('input[name="install_structure_action"]').val();
            $.post(url, params, function(result) {
                if (result.state === 'error') {
                    // ErrorMsg(result.message);
                    app.notice.addErrorMessage(result.message);
                } else {
                    app.progress_bar.update(result.percent);

                    if (result.more > 0) {
                        // var notice_text = js_lang.remainder + result.more + js_lang.piece + " ..." ;
                        // $('#numtips').text(notice_text);

                        app.notice.updateNumTips(result.more);
                        app.task.installStructureSubTask(next);
                    } else {
                        // $('#numtips').remove();
                        app.notice.removeNumTips();
                        app.notice.addSuccessMessage();
                        // SuccessMsg();
                        // installBaseData();
                        next();
                    }
                }
            });
        },

        //安装基础数据
        installBaseDataTask: function(next) {
            console.log('installBaseDataTask');

            app.notice.addSubject(js_lang.install_basic_data)

            let params = {
                db_host: $("#db_host").val(),
                db_port: $("#db_port").val(),
                db_user: $("#db_user").val(),
                db_pass: $("#db_password").val(),
                db_database: $('#db_database').val(),
                db_prefix: $('#db_prefix').val(),
                timezone: $('#timezone').val()
            };

            // notice_html += '<div class="install_notice">' + js_lang.install_basic_data + '</div>';
            // $('#js-notice').html(notice_html);

            let url = $('input[name="install_base_data_action"]').val();
            $.post(url, params, function(result) {
                if (result.state === 'error') {
                    // ErrorMsg(result.message);
                    app.notice.addErrorMessage(result.message);
                } else {
                    // progress(result.percent);
                    app.progress_bar.update(result.percent);
                    // SuccessMsg();
                    app.notice.addSuccessMessage();

                    next();
                }
            });
        },

        //安装演示数据
        installDemoDataTask: function(next) {
            console.log('installDemoDataTask');

            if ($("input[name='js-install-demo']").attr("checked")) {
                // installDemoData();

                app.notice.addSubject(js_lang.install_demo_data)

                let params = {
                    db_host: $("#db_host").val(),
                    db_port: $("#db_port").val(),
                    db_user: $("#db_user").val(),
                    db_pass: $("#db_password").val(),
                    db_database: $('#db_database').val(),
                    db_prefix: $('#db_prefix').val(),
                    timezone: $('#timezone').val()
                };

                let url = $('input[name="install_demo_data_action"]').val();
                $.post(url, params, function(result) {
                    if (result.state === 'error') {
                        // ErrorMsg(result.message);
                        app.notice.addErrorMessage(result.message);
                    } else {
                        // progress(result.percent);
                        app.progress_bar.update(result.percent);
                        // SuccessMsg();
                        app.notice.addSuccessMessage();
                        // createAdminPassport();
                        next();
                    }
                });

            } else {
                // createAdminPassport();
                next();
            }

            // notice_html += '<div class="install_notice">'+ js_lang.install_demo_data + '</div>';
            // $('#js-notice').html(notice_html);


        },

        //创建管理员帐号
        createAdminPassportTask: function(next) {
            console.log('createAdminPassportTask');

            // notice_html += '<div class="install_notice">'+ js_lang.create_administrator_account + '</div>';
            // $('#js-notice').html(notice_html);

            app.notice.addSubject(js_lang.create_administrator_account)

            // var params = "admin_name=" + $("#username").val() + "&"
            //     + "admin_password=" + $("#userpassword").val() + "&"
            //     + "admin_password2=" + $("#confirmpassword").val() + "&"
            //     + "admin_email=" + $("#usermail").val();

            let params = {
                db_host: $("#db_host").val(),
                db_port: $("#db_port").val(),
                db_user: $("#db_user").val(),
                db_pass: $("#db_password").val(),
                db_database: $('#db_database').val(),
                db_prefix: $('#db_prefix').val(),
                timezone: $('#timezone').val(),
                admin_name: $("#user_name").val(),
                admin_password: $("#user_password").val(),
                admin_password_confirm: $("#user_confirm_password").val(),
                admin_email: $("#user_mail").val()
            };

            let url = $('input[name="create_admin_passport_action"]').val();
            $.post(url, params, function(result) {
                if (result.state === 'error') {
                    // ErrorMsg(result.message);
                    app.notice.addErrorMessage(result.message);
                } else {
                    // progress(100);
                    app.progress_bar.update(result.percent);
                    app.notice.addSuccessMessage();
                    // SuccessMsg();
                    next();
                }
            });
        },

        installFinishTask: function (next) {
            console.log('installFinishTask');

            let url = $('input[name="install_finish_action"]').val();
            window.setTimeout(function() {
                location.href = url;
            }, 1000);

            next();

        }

    }

	// var lf = "<br />";
	// var notice = null;
	// var notice_html = '';


})(ecjia.front, jQuery);

//end