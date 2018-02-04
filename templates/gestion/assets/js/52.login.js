var Login = function () {

	var handleLogin = function() {

		$('.login-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            rules: {
	                username: {
	                    required: true
	                },
	                password: {
	                    required: true
	                },
	                remember: {
	                    required: false
	                }
	            },

	            messages: {
	                username: {
	                    required: "Obligatorio"
	                },
	                password: {
	                    required: "Obligatorio"
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit   
	            	$('.alert-danger span').html('Alguno de los campos están vacios!');
	                $('.alert-danger').slideDown(500);
	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                error.insertAfter(element.closest('.input-icon'));
	            },

	            submitHandler: function (form) {
	            	$('.alert-danger').slideUp(500);
	            	$('.login-form').slideUp(400);
	            	$('#LoginBlock').slideDown(400);
	            	var recuerdame="";
	            	if ($('#remember').is(':checked')) { recuerdame="1"; }
	            	$.ajax({
						type: 'POST',
						url: 'security/login',
						data: 'username=' + $('#username').val() + '&password=' + $('#password').val()+'&remember='+recuerdame,
						success:function(msj){
							if ( msj == 1 ){
								if( $('#urlrefer').val() != ''){
									window.location.href = $('#urlrefer').val();
								} else {
									window.location.href = ".";
								}
							}
							if ( msj == 0 ){
								$('.login-form').slideDown(400);
	            				$('#LoginBlock').slideUp(400);
								$('.alert-danger span').html('Nombre de usuario o contraseña inválidos');
	                			$('.alert-danger', $('.login-form')).slideDown(500);		
							}
							if ( msj == -1 ){
								$('.login-form').slideDown(400);
	            				$('#LoginBlock').slideUp(400);
								$('.alert-danger span').html('La cuenta no ha sido activada');
	                			$('.alert-danger', $('.login-form')).slideDown(500);
							}		
							if ( msj == -9 ){
								$('.login-form').slideDown(400);
	            				$('#LoginBlock').slideUp(400);
								$('.alert-danger span').html('Cuenta deshabilitada por el administrador');
	                			$('.alert-danger', $('.login-form')).slideDown(500);
							}				
						},
						error:function(){
							$('.login-form').slideDown(400);
	            			$('#LoginBlock').slideUp(400);
							$('.alert-danger span').html('Error interno');
	                		$('.alert-danger', $('.login-form')).slideDown(500);
						}
					});
					return false;
	            }
	        });

	        $('.login-form input').keypress(function (e) {
	            if (e.which == 13) {
	                if ($('.login-form').validate().form()) {
	                    $('.login-form').submit(); //form validation success, call ajax form submit
	                }
	                return false;
	            }
	        });
	}

	var handleForgetPassword = function () {
		$('.forget-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            ignore: "",
	            rules: {
	                email: {
	                    required: true,
	                    email: true
	                }
	            },

	            messages: {
	                email: {
	                    required: "Obligatorio"
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit   

	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                error.insertAfter(element.closest('.input-icon'));
	            },

	            submitHandler: function (form) {
	                $('.alert-danger').slideUp(500);
	            	$('.forget-form').slideUp(400);
	            	$('#LoginBlock').slideDown(400);
	            	$.ajax({
						type: 'POST',
						url: 'security/do_sendpwd',
						data: 'email=' + $('#email').val(),
						success:function(msj){
							if ( msj == 1 ){
								$('#LoginBlock').slideUp(400);
								$('.alert-success span').html('Se ha enviado un email con instrucciones para cambiar la contraseña.');
	                			$('#SendOK').slideDown(500);
								setTimeout(function() { window.location.href = 'security.php'; },15000);
							}
							if ( msj == 0 ){
								$('.forget-form').slideDown(400);
	            				$('#LoginBlock').slideUp(400);
								$('.alert-danger span').html('No existe ninguna cuenta de usuario vinculada al email facilitado.');
	                			$('.alert-danger', $('.forget-form')).slideDown(500);		
							}
							if ( msj == -9 ){
	            				$('.forget-form').slideDown(400);
	            				$('#LoginBlock').slideUp(400);
								$('.alert-danger span').html('Cuenta deshabilitada por el administrador.');
	                			$('.alert-danger', $('.forget-form')).slideDown(500);		
							}
							if ( msj == -1 ){
								$('.forget-form').slideDown(400);
	            				$('#LoginBlock').slideUp(400);
								$('.alert-danger span').html('Error interno');
	                			$('.alert-danger', $('.forget-form')).slideDown(500);
							}						
						},
						error:function(){
							$('.forget-form').slideDown(400);
	            			$('#LoginBlock').slideUp(400);
							$('.alert-danger span').html('Error interno');
	                		$('.alert-danger', $('.forget-form')).slideDown(500);
						}
					});
					return false;
	            }
	        });

	        $('.forget-form input').keypress(function (e) {
	            if (e.which == 13) {
	                if ($('.forget-form').validate().form()) {
	                    $('.forget-form').submit();
	                }
	                return false;
	            }
	        });

	        $('#back-btn,.back-btn').click(function () {
	            location.href="security";
	        });
	}

	var handleChangePassword = function () {
		$('.change-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            ignore: "",
	            rules: {
	                Form_PassW: {
	                    required: true,
	                    minlength: 6,
	                },
	                Retype_PassW: {
	                    equalTo: "#Form_PassW"
	                },
	            },

	            messages: {
	                Form_PassW: {
	                    required: "Obligatorio"
	                },
	                Retype_PassW: {
	                    required: "Obligatorio"
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit   

	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                error.insertAfter(element.closest('.input-icon'));
	            },

	            submitHandler: function (form) {
	                $('.alert-danger').slideUp(500);
	            	$('.change-form').slideUp(400);
	            	$('#LoginBlock').slideDown(400);
	            	$.ajax({
						type: 'POST',
						url: 'security.php/do_changepwd',
						data: $('.change-form').serialize(),
						success:function(msj){
							if ( msj == 1 ){
								$('#LoginBlock').slideUp(400);
								$('.alert-success span').html('Se ha cambiado la contraseña');
	                			$('#SendOK').slideDown(500);
								setTimeout(function() { window.location.href = 'security.php'; },15000);
							}
							if ( msj == 0 ){
								$('.change-form').slideDown(400);
	            				$('#LoginBlock').slideUp(400);
								$('.alert-danger span').html('No se puede cambiar la contraseña');
	                			$('.alert-danger', $('.change-form')).slideDown(500);		
							}
							if ( msj == -1 ){
								$('.change-form').slideDown(400);
	            				$('#LoginBlock').slideUp(400);
								$('.alert-danger span').html('Error interno');
	                			$('.alert-danger', $('.change-form')).slideDown(500);
							}						
						},
						error:function(){
							$('.change-form').slideDown(400);
	            			$('#LoginBlock').slideUp(400);
							$('.alert-danger span').html('Error interno');
	                		$('.alert-danger', $('.change-form')).slideDown(500);
						}
					});
					return false;
	            }
	        });

	        $('.change-form input').keypress(function (e) {
	            if (e.which == 13) {
	                if ($('.change-form').validate().form()) {
	                    $('.change-form').submit();
	                }
	                return false;
	            }
	        });

	}

	var handleRegister = function () {

		function format(state) {
            if (!state.id) return state.text; // optgroup
            return "<img class='flag' src='assets/global/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
        }


		$("#select2_sample4").select2({
		  	placeholder: '<i class="fa fa-map-marker"></i>&nbsp;Select a Country',
            allowClear: true,
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function (m) {
                return m;
            }
        });


			$('#select2_sample4').change(function () {
                $('.register-form').validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });



         $('.register-form').validate({
	            errorElement: 'span', //default input error message container
	            errorClass: 'help-block', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            ignore: "",
	            rules: {
	                
	                fullname: {
	                    required: true
	                },
	                email: {
	                    required: true,
	                    email: true
	                },
	                address: {
	                    required: true
	                },
	                city: {
	                    required: true
	                },
	                country: {
	                    required: true
	                },

	                username: {
	                    required: true
	                },
	                password: {
	                    required: true
	                },
	                rpassword: {
	                    equalTo: "#register_password"
	                },

	                tnc: {
	                    required: true
	                }
	            },

	            messages: { // custom messages for radio buttons and checkboxes
	                tnc: {
	                    required: "Please accept TNC first."
	                }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit   

	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                if (element.attr("name") == "tnc") { // insert checkbox errors after the container                  
	                    error.insertAfter($('#register_tnc_error'));
	                } else if (element.closest('.input-icon').size() === 1) {
	                    error.insertAfter(element.closest('.input-icon'));
	                } else {
	                	error.insertAfter(element);
	                }
	            },

	            submitHandler: function (form) {
	                form.submit();
	            }
	        });

			$('.register-form input').keypress(function (e) {
	            if (e.which == 13) {
	                if ($('.register-form').validate().form()) {
	                    $('.register-form').submit();
	                }
	                return false;
	            }
	        });

	        jQuery('#register-btn').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.register-form').show();
	        });

	        jQuery('#register-back-btn').click(function () {
	            jQuery('.login-form').show();
	            jQuery('.register-form').hide();
	        });
	}
    
    return {
        //main function to initiate the module
        init: function () {
        	
            handleLogin();
            handleForgetPassword();
            handleChangePassword();
            handleRegister();          
        }

    };

}();