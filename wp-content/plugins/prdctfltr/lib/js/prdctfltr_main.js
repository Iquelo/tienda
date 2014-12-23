(function($){
"use strict";

	var curr_data = [];

	function prdctfltr_submit_form() {
		if ( prdctfltr.click_filter == 'yes' ) {
			var curr = $('#prdctfltr_woocommerce .prdctfltr_woocommerce_ordering');
			curr.find('input[type="hidden"]').each(function() {
				if ( $(this).val() == '' ) {
					$(this).remove();
				}
			});

			curr.submit();
			return false;
		}

	}

		$(document).on('click', 'a#prdctfltr_woocommerce_filter', function(){

			if ( prdctfltr.always_visible == 'no' ) {
				var curr = $(this).parent().children('form');
				if( $(this).hasClass('prdctfltr_active') ) {
					curr.stop(true,true).slideUp(200);
					$(this).removeClass('prdctfltr_active');
				}
				else {

					$(this).addClass('prdctfltr_active')
					curr.css({right: 0}).stop(true,true).slideDown(200);
				}
			}

			return false;
		});


	/*select*/
	$(document).on('click', '.pf_default_select .prdctfltr_widget_title', function() {

		var curr = $(this).parent().next();

		if ( !curr.hasClass('prdctfltr_down') ) {
			curr.prev().find('.prdctfltr-down').attr('class', 'prdctfltr-up');
			curr.addClass('prdctfltr_down');
			curr.slideDown(100);
		}
		else {
			curr.slideUp(100);
			curr.removeClass('prdctfltr_down');
			curr.prev().find('.prdctfltr-up').attr('class', 'prdctfltr-down');
		}

	});

	/*select*/
	$(document).on('click', '.pf_select .prdctfltr_filter > span', function() {

		var curr = $(this).next();

		if ( !curr.hasClass('prdctfltr_down') ) {
			curr.prev().find('.prdctfltr-down').attr('class', 'prdctfltr-up');
			curr.addClass('prdctfltr_down');
			curr.slideDown(100);
		}
		else {
			curr.slideUp(100);
			curr.removeClass('prdctfltr_down');
			curr.prev().find('.prdctfltr-up').attr('class', 'prdctfltr-down');
		}

	});

	/*orderby*/
	$(document).on('click', '.prdctfltr_sale input[type="checkbox"]', function() {

		var curr = $(this).parent();

		if ( !curr.hasClass('prdctfltr_active') ) {
			curr.addClass('prdctfltr_active');
		}
		else {
			curr.removeClass('prdctfltr_active');
		}

	});

	$(document).on('click', '.prdctfltr_orderby input[type="checkbox"]', function() {
		var curr_chckbx =  $(this);

		var curr = $(this).closest('.prdctfltr_filter');
		var curr_var = $(this).val();

		curr.children(':first').val(curr_var);

		curr.find('input:not([type="hidden"])').prop('checked', false);
		curr.find('label').removeClass('prdctfltr_active');
		curr_chckbx.prop('checked', true);
		curr_chckbx.parent().addClass('prdctfltr_active');

		prdctfltr_submit_form();
	});

	$(document).on('click', '.prdctfltr_byprice input[type="checkbox"]', function() {
		var curr_chckbx =  $(this);

		var curr = $(this).closest('.prdctfltr_filter');
		var curr_var = $(this).val().split('-');

		curr.children(':first').val(curr_var[0]);
		curr.children(':first').next().val(curr_var[1]);

		curr.find('input:not([type="hidden"])').prop('checked', false);
		curr.find('label').removeClass('prdctfltr_active');
		curr_chckbx.prop('checked', true);
		curr_chckbx.parent().addClass('prdctfltr_active');

		prdctfltr_submit_form();
	});

	$(document).on('click', '.prdctfltr_characteristics input[type="checkbox"], .prdctfltr_tag input[type="checkbox"], .prdctfltr_cat input[type="checkbox"], .prdctfltr_attributes input[type="checkbox"]', function() {

		var curr_chckbx = $(this);
		var curr = $(this).closest('.prdctfltr_filter');
		var curr_var = $(this).val();

		var curr_attr = curr.children(':first').attr('name');

		if ( curr.hasClass('prdctfltr_cat') ) {
			curr.find('[data-sub='+curr_chckbx.val()+']').slideToggle();
		}

		if ( curr.hasClass('prdctfltr_multi') ) {

			if ( curr_chckbx.val() !== '' ) {
				if ( curr.find('label:first').hasClass('prdctfltr_active') ) {
					curr.find('label:first').removeClass('prdctfltr_active').find('input').prop('checked', false);
				}
				if ( curr_chckbx.parent().hasClass('prdctfltr_active') ) {
					curr_chckbx.prop('checked', false);
					curr_chckbx.parent().removeClass('prdctfltr_active');

					var curr_settings = ( curr.children(':first').val().indexOf(',') > 0 ? curr.children(':first').val().replace(',' + curr_var, '').replace(curr_var + ',', '') : '' );
					curr.children(':first').val(curr_settings);
				}
				else {
					curr_chckbx.prop('checked', true);
					curr_chckbx.parent().addClass('prdctfltr_active');

					var curr_settings = ( curr.children(':first').val() == '' ? curr_var : curr.children(':first').val() + ',' + curr_var );
					curr.children(':first').val(curr_settings);
				}
			}
			else {
				if ( curr_chckbx.parent().hasClass('prdctfltr_active') ) {
					curr_chckbx.prop('checked', false);
					curr_chckbx.parent().removeClass('prdctfltr_active');
				}
				else {
					curr.children(':first').val('');
					curr.find('input:not([type="hidden"])').prop('checked', false);
					curr.find('label').removeClass('prdctfltr_active');
					curr_chckbx.prop('checked', true);
					curr_chckbx.parent().addClass('prdctfltr_active');
				}
			}


		}
		else {

			curr.children(':first').val(curr_var);

			curr.find('input:not([type="hidden"])').prop('checked', false);
			curr.find('label').removeClass('prdctfltr_active');
			curr_chckbx.prop('checked', true);
			curr_chckbx.parent().addClass('prdctfltr_active');
		}



		prdctfltr_submit_form();
	});

	$(document).on('click', '.prdctfltr_sale input[type="checkbox"]', function() {
		prdctfltr_submit_form();
	});



	$(document).on('click', '#prdctfltr_woocommerce span a', function() {

		var curr = $(this).attr('data-key');

		$('#prdctfltr_woocommerce').find('.'+curr+' input[type="hidden"]').each(function() {
			$(this).remove();
		});

		$('#prdctfltr_woocommerce').find('input[type="hidden"]').each(function() {
			if ( $(this).val() == '' ) {
				$(this).remove();
			}
		});

		if ( $('.prdctfltr-widget').length == 0 ) {
			if ( $('#prdctfltr_woocommerce').find('input[type="hidden"]').length == 1 && $('#prdctfltr_woocommerce').find('input[name="sale_products"]:checked').length == 0 ) {
				$('#prdctfltr_woocommerce').find('input[name="filter_results"]').remove();
			}
		}
		else {
			if ( $('#prdctfltr_woocommerce').find('input[type="hidden"]').length == 2 && $('#prdctfltr_woocommerce').find('input[name="sale_products"]:checked').length == 0 ) {
				$('#prdctfltr_woocommerce').find('input[name="filter_results"]').remove();
				$('#prdctfltr_woocommerce').find('input[name="widget_search"]').remove();
			}
		}

		$('#prdctfltr_woocommerce .prdctfltr_woocommerce_ordering').submit();

		return false;
	});


	$(document).on('click', '#prdctfltr_woocommerce_filter_submit', function() {

		var curr = $(this).parent();

		curr.find('input[type="hidden"]').each(function() {
			if ( $(this).val() == '' ) {
				$(this).remove();
			}
		});

		if ( curr.find('input[type="hidden"]').length == 1 && curr.find('input[name="sale_products"]:checked').length == 0 ) {
			curr.find('input[name="filter_results"]').remove();
		}

/*		var curr_string = '';
		var truely = false;
		$('#prdctfltr_woocommerce').find('input[type="hidden"]').each(function() {
			curr_string += ( truely === true ? '&' : '?' )+$(this).attr('name')+'='+$(this).val();
			truely = true;
		});


		var curr_action = $('#prdctfltr_woocommerce .prdctfltr_woocommerce_ordering').attr('action');

		window.location = curr_action+curr_string;*/

		curr.submit();

		return false;
	});

	if ( prdctfltr.custom_scrollbar == 'yes' ) {

		$(".prdctfltr_checkboxes").mCustomScrollbar({
			axis:"y",
			scrollInertia:550,
			autoExpandScrollbar:true,
			advanced:{
				updateOnBrowserResize:true,
				updateOnContentResize:true
			}
		});

		if ( $(".prdctfltr_checkboxes").length > prdctfltr.columns ) {
			if ( $('.prdctfltr-widget').length == 0 ) {

				var curr_scroll_column = $('.prdctfltr_filter:first').width();
				var curr_columns = $('.prdctfltr_filter').length;

				$('.prdctfltr_filter_inner').css('width', curr_columns*curr_scroll_column);
				$('.prdctfltr_filter').css('width', curr_scroll_column);

				$(".prdctfltr_filter_wrapper").mCustomScrollbar({
					axis:"x",
					scrollInertia:550,
					scrollbarPosition:"outside",
					advanced:{
						updateOnBrowserResize:true,
						updateOnContentResize:false
					}
				});
			}
		}
		if ( $('.prdctfltr-widget').length == 0 ) {
			$('.prdctfltr_slide .prdctfltr_woocommerce_ordering').hide();
		}

	}

})(jQuery);