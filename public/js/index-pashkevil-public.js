(function( $ ) {
	'use strict';
	$( document ).ready(function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function( $scope ,jq) {
			debugger;
			if ( $scope.data( 'shake' ) ){
				$scope.shake();
			}
		} );
		if(typeof acf.data != "undefined"){
			$('.acf-taxonomy-field').find("select").attr('data-placeholder', 'בחר');
			acf.data.select2L10n.searching = "מחפש..";
		}
		$(document).delegate(".read_mor", "click", function(e) {
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
			var height = main.find(".extendet_worrper").outerHeight();
				main.find("section").first().animate({
					height: "-="+height
				  }, 300, function() {
					main.find(".extendet_worrper").css("display","none");
					//this_element.one('click', read_mor_cliclk);
					this_element.find("i").removeClass("fa-arrow-up");
					this_element.find("i").addClass("fa-arrow-down");
				  });

		}
		else{ 
		this_element.attr("data-louding","true");
		debugger;     
		this_element.find("i").removeClass("fa-arrow-down");
		this_element.find("i").addClass("fa-spinner fa-spin");
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
		main.find("section").first().animate({
			height: "+="+height
		  }, 300, function() {
			main.find(".extendet_worrper").css("display","block");
			this_element.find("i").addClass("fa-arrow-up");
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
