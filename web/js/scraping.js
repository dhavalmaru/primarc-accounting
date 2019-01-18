$('#scraping_upload').on('submit',function (e) {
		  e.preventDefault();
		  var form = $('#scraping_upload')[0];
		  var formData = new FormData(form);
	      $.ajax({
	        type: 'post',
	        enctype: 'multipart/form-data',
	        url: BASE_URL+'index.php?r=uploadscraping%2Fsaveupload',
	        data: formData,
	        processData: false,
            contentType: false,
            cache: false,
	        success: function () {
	          /*alert("Email has been sent!");*/
	        }
	      });
    });