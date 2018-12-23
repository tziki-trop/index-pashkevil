(function( $ ) {
    'use strict';

    $( document ).ready(function() {
         if(typeof loadmore_params === "undefined")
         return;
        $(window).scroll(function(){
            
              var data = {
                  'action': 'loadmore',
                  'query': loadmore_params.posts,
                  'page' : loadmore_params.current_page
              };
              
              if(parseInt(loadmore_params.canBeLoaded) == true && $(window).scrollTop() + $(window).height() > $(document).height() - parseInt(loadmore_params.bottomOffset)) {

             // if(  parseInt(loadmore_params.canBeLoaded) == true ){
                 
                  $("#loudmor").removeClass("nutactive");
                  $.ajax({
                      url : loadmore_params.ajaxurl,
                      data:data,
                      type:'POST',
                      beforeSend: function( xhr ){
                          // you can also add your own preloader here
                          // you see, the AJAX call is in process, we shouldn't run it again until complete
                          loadmore_params.canBeLoaded = false; 
                      },
                      success:function(data){
                        
debugger;
                          if( data ) {
                               $(data).insertAfter($(".all_busienss").find(".one_bus").last());
                             // $('#content').find('.endofpost:last').append( data ); // where to insert posts
                             // $("#loudmor").addClass("nutactive");
                              loadmore_params.canBeLoaded = true; // the ajax is completed, now we can run it again
                              loadmore_params.current_page++;
                          }
                      }
                  });
              }
          
      });

    });
})( jQuery );

	

