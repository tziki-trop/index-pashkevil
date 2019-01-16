var form_submited = false;
(function( $ ) {
	'use strict';
/*	$(document).ready(function () {
		var postID = acf.get('post_id');
	
		$("#submit_acf_form a").click(function (e) { 
			debugger;
			e.preventDefault();
			$(".acf-button").click();
	
		});	
		jQuery(function($) {
			$('#acf-form').on('submit', (e) => {
				debugger;
			//	if(!$(e.target).hasClass("ajex")){
			//		return true;
			//	}
				if(form_submited){
					e.preventDefault();
					return false;
				}
			//	debugger;
			var speener =	$(e.target).find(".acf-spinner");
			//var speener =	$(this).find(".acf-spinner");
			speener.css("display","inline-block");
			speener.addClass("is-active");
				form_submited = true;
				
				let form = $(e.target);
				e.preventDefault();
				form.submit(function(event) { event.preventDefault(); submitACF_AJAX(this); return false;});
		
			});
		});


	});
*/
	function submitACF_AJAX(form) {
	//	debugger;
		var data = new FormData(form);
		//acf.lockForm( form );
		//acf.validation.toggle( form, 'lock' );
	//	acf.validation.lockForm( form );
		//acf.validation.showSpinner($spinner);
		$.ajax({
        type: 'POST',
        dataType: "json",
		url: window.location.href,
		data: data,
		processData: false,
		contentType: false
		})
		.done(function(data) {
            debugger;
            if(data.ststus !== "undefined" && data.ststus === "error" ){
                debugger;
                var error = "<div class=\"acf-notice\"><p>"+data.dis +"</p></div>";
                //field_5c3dc6dc93b5e
                var fild = $(".acf-field-"+data.field);
               // $(content).appendTo(selector);
                $(error).appendTo(fild);
              //  $(this).append(data);
                }
                debugger;
         
			form_submited = false;
	     
			$(form).find(".acf-spinner").css("display","none");

			$(form).find(".acf-spinner").removeClass("is-active");
			$(form).trigger('acf_submit_complete_', data);
	  })
		.fail(function(error) {
		$(form).trigger('acf_submit_fail', error);
	  });
	}
  
	function renderPage() {
		// initialize the acf script
		acf.do_action('ready', $('body'));
	  
		// will be used to check if a form submit is for validation or for saving
		let isValidating = false;
	  
		acf.add_action('validation_begin', () => {
		  isValidating = true;
		});
	  
		acf.add_action('submit', ($form) => {
		  isValidating = false;
		});
	  
		$('.acf-form').on('submit', (e) => {
			debugger;
		  let $form = $(e.target);
		  e.preventDefault();
		  // if we are not validating, save the form data with our custom code.
		  if( !isValidating ) {
			// lock the form
			acf.validation.toggle( $form, 'lock' );
			$.ajax({
			  url: window.location.href,
			  method: 'post',
			  data: $form.serialize(),
			  success: () => {
				// unlock the form
				acf.validation.toggle( $form, 'unlock' );
			  }
			});
		  }
		});
	  }

})( jQuery );