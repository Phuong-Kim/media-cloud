jQuery(document).ready(function ($) {
    if (typeof ajaxurl === 'undefined') {
        ajaxurl = MC_obj.ajaxurl;
    }

    // $('#MCkey').click(function (e) {
    //     e.preventDefault();
    //     const formData = new FormData(e.currentTarget);
    //     formData.append('action', 'apikey_ajax_function');

    //     if (validateForm()) {
    //         axios.post(ajaxurl, formData)
    //             .then(function (response) {
    //             })
    //             .catch(function (error) {
    //             });
    //         // jQuery('#exampleModal').modal('show');
    //     } else {
    //         // alert('Validation failed!');
    //     }
    // });


    $('#check_key').submit(function (e) {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        formData.append('action', 'check_key_ajax_function');

        //         if (validateForm()) {
        if (true) {
            axios.post(ajaxurl, formData)
                .then(function (response) {
                    customAlert(response.data.data).done(function () {
                        window.location.reload();
                    });
                })
                .catch(function (error) {
                });
            // jQuery('#exampleModal').modal('show');
        } else {
        }
    });

    function customAlert(message) {
        var deferred = $.Deferred();
        window.alert(message);

        $(window).on('focus', function () {
            deferred.resolve();
        });

        return deferred.promise();
    }


    function validateForm() {
        var form = document.getElementById('MCkey');
        var isValid = form.checkValidity();
        var checkboxes = form.querySelectorAll('input[type="checkbox"]');
        if (!isValid) {
            var inputs = form.querySelectorAll('input[required]');
            for (var i = 0; i < inputs.length; i++) {
                if (!inputs[i].checkValidity()) {
                    isValid = false;
                    break;
                }
                if (!inputs[i].checkValidity()) {
                    showErrorMessage(inputs[i]);
                }
            }
        } else {
            isValid = true;
            // isValid = false;
            // for (var i = 0; i < checkboxes.length; i++) {
            //     if (checkboxes[i].checked) {
            //         isValid = true;
            //         break;
            //     }
            // }
        }

        return isValid;
    }
    $('input[required]').change(function () {
        hideErrorMessage(this);
    });

    function showErrorMessage(input) {
        var errorMessage = input.getAttribute('data-error-message');
        if (!errorMessage) {
            errorMessage = 'This field is required.';
        }

        var errorElement = $('<div class="error-message">' + errorMessage + '</div>');
        $(input).addClass('error');
        $(input).after(errorElement);
    }

    function hideErrorMessage(input) {
        $(input).removeClass('error');
        $(input).next('.error-message').remove();
    }

    //Delete file
    $("#deleteFile").val(true);
    $("#deleteFile").change(function () {
        if ($(this).is(":checked")) {
            $(this).val(true);
        } else {
            $(this).val(false);
        }
    });

    //   console.log(ajaxurl);
    $("#Sync_media").click(function (e) {
        // e.preventDefault();

        $.ajax({
            data: {
                action: "get_file_media_cloud",
            },
            url: ajaxurl,
            method: "post",
            beforeSend: function (xhr) { },
            success: function (response) {
                if (response.success) {
                    $("#upload").prop("disabled", false);
					
                    postajax(response);
                    //                     if (requestAjax) {
                    //                             //console.log("done");
                    //                             return;
                    //                         }
                } else {
                    //                         alert("NOTICE: Please Upload & Setup Favicon after Sync complete!");

                    $("#upload").prop("disabled", true);
                    alert('Empty folder, no need to bring it up');
                    $(".check-load").fadeOut();
                    $(".check-warning").show();
                }
            }
        })
    });

    function postajax(response) {
        var data_post = response.data;
        var total = `<span class='fs-6 fst-italic fw-bold total-file'>${data_post.length} files to upload</span>`;
		$("#data-passed").html('');
        $("#total").html(total);
        $(".check-load").hide();
        $(".check-success").show();
		jQuery('#mediaModal').modal('show');
        $("#upload").click(function (e) {
			$("#delete").prop("disabled", false);
            e.preventDefault();
            $(".loading").fadeIn();
			var totalCount = data_post.length;
			var fileShow = data_post.length;
			var count = 0;
			var path = data_post[count];
			var n = 0;
            function sendRequest(path) {
				$.ajax({
					data: {
						action: "post_media_function",
						path: path,
					},
					url: ajaxurl,
					method: "post",
					beforeSend: function (xhr) { },
					success: function (result) {
						$(".loading").fadeOut();
						count++;
						var progressBarWidth = (count / totalCount) * 100;
						$(".progress-bar").css("width", progressBarWidth + "%");
						if (result.data) {
							image = result.data;
							$("#data-passed").append(image);
							var each = `<span class='fs-6 fst-italic fw-bold each-image'>${count}/</span>`;
							$("#each").html(each);
							if (count < totalCount) {
							sendRequest(data_post[count]);
							} else {
								var each = `<span class='fs-6 text-success fw-bold'>Upload completed/</span>`;
								$("#each").html(each);
							}
						}

					},
					error: function (xhr, status, error) {
						n++;
						if (n <= 5) {
							sendRequest(data[count]);
						} else {
		                    console.log(xhr.responseText);
		                    console.log(status);
		                    console.log(error);
						}
					},
				});
			}
			sendRequest(data_post[count]);
            
            $("#upload").prop("disabled", true);
        });
    }

    

    // Delete
    $("#delete_media").click(function (e) {
        e.preventDefault();
		$(".check-load").show();
        var dell = confirm("We will only delete files you already have on Media Cloud, avoiding loss of your data!!");
        var delAll = confirm("Are you sure to delete all?");
        if (dell) {
            if (delAll) {
				$("#delete").prop("disabled", true);
				Swal.fire({
					title: 'Processing...',
					text: 'Please wait until processing is completed!!!',
					allowOutsideClick: false,
					showConfirmButton: false,
					onBeforeOpen: () => {
						Swal.showLoading();
					}
				});
                $.ajax({
                    data: {
                        action: "delete_media_cloud",
                    },
                    url: ajaxurl,
                    method: "post",
                    beforeSend: function (xhr) { },
                    success: function (result) {
                        if (result.success) {
							Swal.close();
                            $(".get-image").hide();
                            var noti = `<p class='fs-6 fst-italic fw-bold total-file'>All files Deleted</p>`;
                            $("#each").html(noti);
							
                        } else {
							Swal.close();
							$("#delete").click();
                        }
						$(".check-load").hide();
                        $("#total").hide();
						$("#data-passed").html('');
                    },
                });
            };
        };
    });

    //Recover
    $("#recover_media").click(function (e) {
        e.preventDefault();
		$(".check-load").show();
		$("#delete").prop("disabled", false);
		Swal.fire({
            title: 'Processing...',
            text: 'Please wait until processing is completed!!!',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
		
        $.ajax({
            data: {
                action: "recover_cloud",
            },
            url: ajaxurl,
            method: "post",
            success: function (result) {
                if (result.success) {
					if(result.data == 'Start_again'){
					   $("#recover").click();
					} else {
						Swal.close();
                    	$('#media').click();
						$("#delete").prop("disabled", false);
						$("#fileProgress").show();
						$(".get-image").hide();
						$(".check-warning").hide();
						var noti = `<p class='fs-6 fst-italic fw-bold text-success'>Recover Completed</p>`;
						$("#each").html(noti);
                        $("#total").hide();
					}

                } else {
                    alert('Failed to Recover');
                }
				$(".check-load").hide();
            }
        });
    });

    // $("#server_media").click(function (e) {
    //     $.ajax({
    //         data: {
    //             action: "back_up_server",
    //         },
    //         url: ajaxurl,
    //         method: "POST",
    //         success: function (result) {
    //             console.log(result);
    //         },
    //     });
    // });
})
