(function( $ ) {
	'use strict';
	$( document ).ready(function() {
		
		if(typeof acf.data != "undefined"){
			$('.acf-taxonomy-field').find("select").attr('data-placeholder', 'בחר');
			acf.data.select2L10n.searching = "מחפש..";
		}
      //  $("#testcontainer").on("submit", ".frm_add", function (event){
    $(document).delegate("form", "submit", function(e) {
		if($(e.target).closest("form").attr("id") != "lead_to_client")
        return;
      //  var filds = {};
		var form = $(e.target).closest("form");
		form.find(".elementor-button-text").append("<i class=\"fa fa-spinner fa-spin\"></i>");
       var filds = {action : "l_t_c"};
        $.each($(e.target).closest("form").find("input"), function( index, fild ) {
        var name = $(fild).attr("name");
        filds[name] = $(fild).val();
      //  alert( index + ": " + value );
        });
       // filds['action'] = 'l_t_c';
        debugger;
     console.log('test');
     e.preventDefault();
    // alert('test');
     /* $.ajax function will go here to save row */
  		$.ajax({
			type: 'POST',
			 dataType: 'json',
		 crossDomain: true,
			url: "/wp-admin/admin-ajax.php",
			
			data:  //action: 'l_t_c',
                   filds
				//id: id,
				//type: type,
				//nons: nons,	
			,       
			success: function (data) {
				if(data[0] === true){
					form.find("input").val('');
					form.find("input").attr('value','');
					form.find(".fa-spinner").remove();
				}
				//if(data.status === "seccsee"){
					debugger;

				//	show_mor(this_element,data);
		
				//}       
					//this_element.closest(".one_bus").find(".extendet_worrper")
		
			}
		  
			});
  });
		$(document).delegate(".read_mor", "click", function(e) {
            debugger;
		//$(".read_mor").one('click', read_mor_cliclk);
		read_mor_cliclk(e);
	
		});
	});
	var read_mor_cliclk = function(e){
		debugger;
		e.preventDefault();
		e.stopPropagation();
		var this_element = $(e.target);
		if(!this_element.hasClass("read_mor"))
		 this_element = this_element.closest(".read_mor");
		if(this_element.attr("data-louding") === "true")
		return;

		debugger;
		
		if(this_element.hasClass("activ")){
			this_element.removeClass("activ");
			var main = this_element.closest(".one_bus");
			//var height = main.find(".extendet_worrper").outerHeight();
			var height =	main.attr("data-extend_h");

				main.find("section").first().animate({
					height: "-="+height
				  }, 300, function() {
					main.find(".extendet_worrper").css("display","none");
					//this_element.one('click', read_mor_cliclk);
					this_element.find("i").removeClass("fa-angle-up");
					this_element.find("i").addClass("fa-angle-down");
				  });

		}
		else{ 
		this_element.attr("data-louding","true");
		debugger;     
		this_element.find("i").removeClass("fa-angle-down");
		this_element.find("i").addClass("fa-spinner fa-spin spin_loud_more");
		
		if(this_element.attr("data-louded") === "true"){
			show_mor(this_element);
		}
		else{
		var id = this_element.closest(".one_bus").attr("data-bus-id");
		var type = this_element.closest(".one_bus").attr("data-bus-type");
		var nons = $("input[name='ajax_nonce']").val();
		debugger;
					  
			$.ajax({
			type: 'POST',
			 dataType: 'json',
		 crossDomain: true,
			url: "/wp-admin/admin-ajax.php",
			
			data: { action: 'get_business',
				id: id,
				type: type,
				nons: nons,	
			},       
			success: function (data) {
				if(data.status === "seccsee"){
					debugger;

					show_mor(this_element,data);
		
				}       
					//this_element.closest(".one_bus").find(".extendet_worrper")
		
			}
		  
			});
		}
		}
	}
	var show_mor = function(this_element,data){
		data = data || false;
		var section = this_element.closest("section");
		var main = this_element.closest(".one_bus");
		if(data != false){
			$("<div class='extendet_worrper' style='display: none;'>"+data.data+"</div>").insertAfter( section);
		}  
		var height = main.find(".extendet_worrper").outerHeight();
		main.attr("data-extend_h",height);
		main.find("section").first().animate({
			height: "+="+height
		  }, 300, function() {
			main.find(".extendet_worrper").css("display","block");
			this_element.find("i").addClass("fa-angle-up");
			this_element.find("i").removeClass("fa-spinner fa-spin");
			this_element.addClass("activ");
			this_element.attr("data-louding","false");
			this_element.attr("data-louded","true");
			this_element.one('click', read_mor_cliclk);
		  });
	}
	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
