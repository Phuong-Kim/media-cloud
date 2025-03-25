jQuery(document).ready(function() {
//             $("#Login").click(function() {
//                 var Email = $('#Email').val();
//                 var Password = $('#Password').val();
//                 let Form = new FormData();
//                     Form.append("Password", Password);
//                     Form.append("Email", Email);
//                     $.ajax({
//                         type: "POST",
//                         url: 'loginAcc.php',
//                         dataType: "text",
//                         async: false,
//                         enctype: 'multipart/form-data',
//                         processData: false,
//                         contentType: false,
//                         data: Form,
//                         success: function(data) {
//                             if (data == "The email entered does not belong to any account!") {
//                                 $("#message").html(data);
//                                 $("#message").prop("class", "text-danger");
//                             }  else if (data == "wrong password!") {
//                                 $("#reminderPassword").html(data);
//                                 $("#reminderPassword").prop("class", "text-danger");
//                             } else if (data == "Unconfimred"){
//                                 window.location.replace("confirmemail.php");
//                             } else {
//                                 window.location.replace("index.php");
//                             }
//                         }
//                     });
//             });
            $("#submit_Verify").click(function() {
                var Verify = $('#verify').val();
                let Form = new FormData();
                    Form.append("token", Verify);
					Form.append("action", 'verify_cloud');
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        dataType: "text",
                        async: false,
                        enctype: 'multipart/form-data',
                        processData: false,
                        contentType: false,
                        data: Form,
                        success: function(data) {
							var data = JSON.parse(data);
							console.log(data.data);
                            if (data.data == "Verified successfully!") {
								sessionStorage.setItem('successMessage', 'Verified successfully!');
                                window.location.reload();
                            } else {
                             Swal.fire({
								  title: 'Error:',
								  text: data.data,
								  allowOutsideClick: false,
								  showConfirmButton: true,
								  onBeforeOpen: () => {
									Swal.showLoading();
								  }
								});
                            }
                        }
                    });
            });
	
			window.addEventListener('load', function() {
			  var successMessage = sessionStorage.getItem('successMessage');
			  if (successMessage) {
				Swal.fire({
				  title: 'Success:',
				  text: successMessage,
				  allowOutsideClick: false,
				  showConfirmButton: true,
				  onBeforeOpen: () => {
					Swal.showLoading();
				  }
				});
				sessionStorage.removeItem('successMessage');
			  }
			});	
			window.addEventListener('load', function() {
			  var errorMessage = sessionStorage.getItem('errorMessage');
			  if (errorMessage) {
				Swal.fire({
				  title: 'Error:',
				  text: errorMessage,
				  allowOutsideClick: false,
				  showConfirmButton: true,
				  onBeforeOpen: () => {
					Swal.showLoading();
				  }
				});
				sessionStorage.removeItem('errorMessage');
			  }
			});	
	
			$("#submit_forgot").click(function() {
                var Email = $('#email').val();
                let Form = new FormData();
                    Form.append("email", Email);
					Form.append("action", 'forgot_cloud');
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        dataType: "text",
                        async: false,
                        enctype: 'multipart/form-data',
                        processData: false,
                        contentType: false,
                        data: Form,
                        success: function(data) {
							var data = JSON.parse(data);
                            if (data.data == "Email sent successfully, check your inbox!") {
                                 jQuery('#forgot_Modal').modal('hide');
								 jQuery('#Verify_Modal').modal('show');
                            } else {
                             //window.location.replace("confirmemail.php");
                            }
                        }
                    });
            });
            $("#Email").change(function() {
                $("#message").html('');
            });$("#Password").change(function() {
                $("#reminderPassword").html('');
            });
            $(".show-btn").click(function() {
                let passField = $(this).siblings("input");
                if(passField.attr('type') === "password"){
                passField.prop("type", "text");
                $(this).children().prop("class", "fa-solid fa-eye-slash");
                }else{
                passField.prop("type", "password");
                $(this).children().prop("class", "fa-solid fa-eye");
                }
            });
            //insert
            const modal = $('#modal');
            const show = $('#showmodal');
            const span = $('.close');
            show.click(function() {
                modal.show();
            });
            span.click(function() {
                modal.hide();
            });
            // $(window).on('click', function(e) {
            //     if ($(e.target).is('#modal')) {
            //         modal.hide();
            //     }
            // });
            $("#Name").change(function(){
                name();
            });
            $("#create").change(function(){
                create();
            });
            $("#retry").change(function(){
                retry();
            })
            ;
            $("#mail").change(function(){
                mail();
            })
            const nameformat = /^[a-zA-Z0-9]+$/;
            function name() {
                const nameVal = $("#Name").val(); 
                if (nameVal == ''){
                    $("#reminderName").html("You must enter Account name ");
                    $("#reminderName").prop("class", "text-danger");
                }else if (nameformat.test(nameVal)){
                    $("#reminderName").html("You have entered a Valid Account name address!");
                    $("#reminderName").prop("class", "text-success");
                }else {
                    $("#reminderName").html("Account names are Latin letters and numbers from 0 to 9");
                    $("#reminderName").prop("class", "text-danger");
                }
            }
            const createformat = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;
            
            function create() {
                const createVal = $("#create").val(); 
                if (createVal == ''){
                    // console.log(inpval)
                    $("#reminderCreate").html("You must enter your password");
                    $("#reminderCreate").prop("class", "text-danger");
                }else if (createformat.test(createVal)){
                    $("#reminderCreate").html("You have entered a Valid password address!");
                    $("#reminderCreate").prop("class", "text-success");
                }else {
                    $("#reminderCreate").html("Password containing at least 8 characters, 1 number, 1 upper and 1 lowercase");
                    $("#reminderCreate").prop("class", "text-danger");
                }
            }
            
            function retry() {
                const createVal = $("#create").val(); 
                const retryVal = $("#retry").val(); 
                if (retryVal == createVal){
                    $("#reminderRetry").html("Work confirmation!");
                    $("#reminderRetry").prop("class", "text-success");
                }else {
                    $("#reminderRetry").html("Wrong password, please re-enter!");
                    $("#reminderRetry").prop("class", "text-danger");
                }
            }
            const mailformat = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            function mail() {
                const mailVal = $("#mail").val(); 
                if (mailVal == ''){
                    $("#reminderMail").html("You must enter your email");
                    $("#reminderMail").prop("class", "text-danger");
                } else if (mailformat.test(mailVal)){
                    $("#reminderMail").html("You have entered a Valid email address!");
                    $("#reminderMail").prop("class", "text-success");
                }else {
                    $("#reminderMail").html("You must enter your email in the correct format your email.@domain.");
                    $("#reminderMail").prop("class", "text-danger");
                }
            }
            $("#submit_sign").click(function() {
                name();
                mail();
                create();
                retry();
                const nameVal = $("#Name").val(); 
                const createVal = $("#create").val(); 
                const retryVal = $("#retry").val(); 
                const mailVal = $("#mail").val(); 
                if ( (nameformat.test(nameVal))&&(createformat.test(createVal))&&(mailformat.test(mailVal))&&(retryVal == createVal)) {
                    let Form = new FormData();
                    Form.append("name", nameVal);
                    Form.append("password", createVal);
                    Form.append("gmail", mailVal);
                    Form.append("action", 'sign_up_cloud');
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        dataType: "text",
                        async: false,
                        enctype: 'multipart/form-data',
                        processData: false,
                        contentType: false,
                        data: Form,
                        success: function(data) {
							var data = JSON.parse(data);
                             if (data.data == "Email sent successfully, check your inbox!") {
								 jQuery('#Cr_Acc_Modal').modal('hide');
								 jQuery('#Verify_Modal').modal('show');
                            } else {
                             $("#reminderMail").prop("class", "text-danger");
								$('#reminderMail').html(data.data);
                            }
                        }
                    });
                    
                }
                
            });
//             if($('#sign-out')){
//                 $('#sign-out').click();
//             }
        });