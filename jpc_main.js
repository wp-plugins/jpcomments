/*

	jpComments Javascript File


*/

console.log('I loaded');

/*

	Input Hover Text Replacement

*/

jQuery('#respond input:not([type=submit]), #respond textarea').each(function(n, el){

	console.log('unce');

	if(!jQuery(this).hasClass('no_remove')){

		var val = jQuery(el).val();
		
		jQuery(el).focus(function(){
			
			if(jQuery(this).val() == val){
				jQuery(this).val('');
				jQuery(this).addClass('input_active');
			}
			
		});
	
		jQuery(el).blur(function(){
			
			if(jQuery(this).val() == ''){
				jQuery(this).val(val);
				jQuery(this).removeClass('input_active');
			}
			
		});

	}
	
});

/*

	Remove spaces from a twitter usernam

*/

jQuery('#author').keyup(function(){
	
	jQuery(this).val(jQuery(this).val().replace(' ',''));

});

/*

	Quick hack to stop the comments from submitting blank comments

*/

if(jQuery('#respond input#submit').length){

	jQuery('#respond input#submit').click(function(event){
	
		if(jQuery('#author').val() == 'Twitter Username' || jQuery('#comment').val() == 'Write a comment...'){
		
			event.preventDefault();
			return false;		
		}
	
	});
	
}