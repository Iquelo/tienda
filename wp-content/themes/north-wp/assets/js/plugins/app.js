var favicon;

(function ($, window) {
	'use strict';
    
    var lastTime = 0,
        vendors = ['ms', 'moz', 'webkit', 'o'];
	
    for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame'] || window[vendors[x]+'CancelRequestAnimationFrame'];
    }
 
    if (!window.requestAnimationFrame) {
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() { callback(currTime + timeToCall); },
              timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };
    }
 
    if (!window.cancelAnimationFrame){
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
    }
    
	var $doc = $(document),
		win = $(window),
		Modernizr = window.Modernizr,
		thb_easing = [0.25, 0.1, 0.25, 1];

	var SITE = SITE || {};
	
	SITE = {
		init: function() {
			var self = this,
					obj;
			
			for (obj in self) {
				if ( self.hasOwnProperty(obj)) {
					var _method =  self[obj];
					if ( _method.selector !== undefined && _method.init !== undefined ) {
						if ( $(_method.selector).length > 0 ) {
							_method.init();
						}
					}
				}
			}
		},
		SmoothScroll: {
			selector: '.smooth_scroll',
			init: function() {
				smoothScroll();
			}
		},
		headRoom: {
			selector: '.header',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				win.scroll(function(){
					base.scroll(container);
				});
			},
			scroll: function (container) {
				var animationOffset = container.data('offset'),
						wOffset = win.scrollTop(),
						stick = container.data('stick-class'),
						unstick = container.data('unstick-class');
						
				if (wOffset > animationOffset) {
					container.removeClass(unstick);
					if (!container.hasClass(stick)) {
						setTimeout(function () {
							container.addClass(stick);
						}, 10);
					}
				} else if ((wOffset < animationOffset && (wOffset > 0))) {
					if(container.hasClass(stick)) {
						container.removeClass(stick);
						container.addClass(unstick);
					}
				} else {
					container.removeClass(stick);
					container.removeClass(unstick);
				}
			}
			
		},
		responsiveNav: {
			selector: '#wrapper',
			init: function() {
				var base = this,
						container = $(base.selector),
						cc = $('.click-capture'),
						target = $('#quick_cart, .mobile-toggle').data('target'),
						children = $('#mobile-menu').find('.animation'),
						parents = $('#mobile-menu').find('.mobile-menu>li:has(".sub-menu")>a');
				
				
				$('#quick_cart, .mobile-toggle').on('click', function() {
					var that = $(this),
							target= that.data('target'),
							t = -1,
							z = -1;

					container.removeClass('open-menu open-cart').addClass(target);

					children.removeClass('animate').delay(450).each(function () {
						var that = $(this);
								t++;
						
						setTimeout(function () {
							that.addClass("animate");
						}, 200 * t);
						
					});
					
					$('#side-cart').find('.animation').removeClass('animate').delay(450).each(function () {
						var that = $(this);
								z++;
						
						setTimeout(function () {
							that.addClass("animate");
						}, 200 * z);
						
					});
					
					SITE.customScroll.init();
					return false;
				});
				cc.on('click', function() {
					container.removeClass('open-menu open-cart');
					children.find('.sub-menu').hide();
				});
				
				parents.live('click', function(){
					var that = $(this);
					parents.filter('.active').not(this).removeClass('active').next('.sub-menu').slideUp();
					
					if (that.hasClass('active')) {
						that.removeClass('active').next('.sub-menu').slideUp();
					} else {
						that.addClass('active').next('.sub-menu').slideDown();
					}
					
					return false;
				});
				
			}
		},
		updateCart: {
			selector: '#quick_cart',
			init: function() {
				var base = this,
					container = $(base.selector);
				$('body').bind('added_to_cart', SITE.updateCart.update_cart_dropdown);
			},
			update_cart_dropdown: function(event) {
				if ($('body').hasClass('woocommerce-cart')) {
					location.reload();	
				} else {
					$('#quick_cart').trigger('click');	
				}
			}
		},
		navDropdown: {
			selector: '#nav .sf-menu',
			init: function() {
				var base = this,
						container = $(base.selector),
						item = container.find('>li.menu-item-has-children');
						
					item.each(function() {
						var that = $(this),
								offset = that.offset(),
								dropdown = that.find('>.sub-menu, >.thb_mega_menu_holder'),
								children = that.find('li.menu-item-has-children'),
								menuoffset = 0,
								pageoffset = 0,
								megamenuoffsetleft = 0,
								megamenuoffsetright = 0;

						that.hoverIntent(
							function () {
								that.addClass('sfHover');
								menuoffset = Math.floor(parseInt($(this).find('>a').css('margin-left'), 10) - 18);
								pageoffset = Math.floor(((win.width() - $('.row').width()) / 2 ) +15);
								offset.right = (win.width() - (offset.left + that.outerWidth()));
								megamenuoffsetleft = offset.left - pageoffset;
								megamenuoffsetright = offset.right - pageoffset;
								dropdown.filter('.sub-menu').css({
									'left': menuoffset
								});
								dropdown.filter('.thb_mega_menu_holder').css({
									'left': - megamenuoffsetleft,
									'right': - megamenuoffsetright
								});
								dropdown.fadeIn();
								$(this).find('>a').addClass('active');
								
							},
							function () {
								that.removeClass('sfHover');
								dropdown.hide();
								$(this).find('>a').removeClass('active');
							}
						);
						
						children.hoverIntent(
							function () {
								that.addClass('sfHover');
								$(this).find('>.sub-menu').fadeIn();
								$(this).find('>a').addClass('active');
								
							},
							function () {
								that.removeClass('sfHover');
								$(this).find('>.sub-menu').hide();
								$(this).find('>a').removeClass('active');
							}
						);
					});
					
			}
		},
		fullWidth: {
			selector: '.full-width-section',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				base.resize(container);
				
				win.resize(function() {
					base.resize(container);
				});
			},
			resize: function(container) {
				var body = $('body'),
					outerContainer = (body.hasClass('boxed') ? $('#wrapper') : win ),
					w = outerContainer.width(),
					OutMargin = Math.floor( ((w - Math.floor(container.parents('.post-content').width())) / 2) );
				

				container.each(function(){
					var that = $(this);
					if (body.hasClass('rtl')) {
						that[0].style.setProperty( 'margin-right', - OutMargin + 'px', 'important' );
					} else {
						that[0].style.setProperty( 'margin-left', - OutMargin + 'px', 'important' );
					}
					that[0].style.setProperty( 'padding-left', OutMargin + 'px', 'important' );
					that[0].style.setProperty( 'padding-right', OutMargin + 'px', 'important' );
					that[0].style.setProperty( 'visibility', 'visible');
				});
			}
		},
		fullWidthContent: {
			selector: '.full-width-content',
			init: function() {
				var base = this,
					container = $(base.selector);
				
				base.resize(container);
				win.resize(function() {
					base.resize(container);
				});
				
			},
			resize: function(container) {
				var body = $('body'),
					outerContainer = (body.hasClass('boxed') ? $('#wrapper') : win ),
					w = outerContainer.width(),
					OutMargin = Math.ceil( ((w - Math.floor(container.parents('.post').width())) / 2) );
				container.each(function(){
					if (body.hasClass('rtl')) {
						$(this).css({
						'margin-right': - OutMargin,
						'width': w
						});
					} else {
						$(this).css({
							'margin-left': - OutMargin,
							'width': w
						});
					}
				});
			}
		},
		fullHeightContent: {
			selector: '.full-height-content',
			init: function() {
				var base = this,
					container = $(base.selector);
				
				base.resize(container);
				win.resize(function() {
					base.resize(container);
				});
				
			},
			resize: function(container) {
				container.height(win.outerHeight());
			}
		},
		carousel: {
			selector: '.owl',
			init: function() {
				var base = this,
					container = $(base.selector),
					flag = false;
						
				container.each(function() {
					var that = $(this),
						columns = that.data('columns'),
						center = (that.data('center') === true ? true : false),
						navigation = (that.data('navigation') === true ? true : false),
						autoplay = (that.data('autoplay') === false ? false : true),
						pagination = (that.data('pagination') === true ? true : false),
						autowidth = (that.data('autowidth') === true ? true : false),
						bgcheck = (that.data('bgcheck') ? that.data('bgcheck') : false),
						loop = (that.data('autowidth') === true ? false : (that.data('loop') === true ? true : false)),
						duration = 300,
						thumbs = $('#product-thumbnails');

					if (that.find('img').length < 2) { 
						loop = false;
						navigation = false;
					}

					that.owlCarousel({
						nav: navigation,
						dots: true,
						autoplayHoverPause: true,
						autoplay: autoplay,
						autoplayTimeout: 5000,
						center: center,
						loop: loop,
						navSpeed: 1200,
						autoWidth: autowidth,
						items: columns,
						responsiveRefreshRate: 100,
						responsive: {
							0: {
								items: 1
							},
							768: {
								items: (columns < 2 ? columns : 2)
							},
							980: {
								items: (columns < 3 ? columns : 3)
							},
							1200: {
								items: columns
							}
						},
						onInitialized: function() {
							if (bgcheck) {
								BackgroundCheck.init({
									targets: base.selector,
									images: base.selector +' img',
									minComplexity: 80,
									maxDuration: 1500,
									minOverlap: 0
								});
							}
							if (that.hasClass('lookbook-container')) {
								that.on('mousewheel', '.owl-stage', function (e) {
									if (e.deltaY>0) {
										that.trigger('next.owl');
									} else {
										that.trigger('prev.owl');
									}
									e.preventDefault();
								});
								that.find('.look, img').height(win.outerHeight());
							}
						},
						onChanged: function() {
							if (that.hasClass('lookbook-container')) {
								that.find('.look').removeClass('active');
								that.find('.look, img').height(win.outerHeight());
							}
						},
						onResized: function() {
							if (that.hasClass('lookbook-container')) {
								that.find('.look').removeClass('active');
								that.find('.look, img').height(win.outerHeight());
							}
						}
					}).on('changed.owl.carousel', function (e) {
						if (bgcheck) {
							setTimeout(function() {
								BackgroundCheck.refresh();
							}, 1250);	
						}
						if (that.hasClass('product-images') && thumbs) {
							$('.product-thumbnails').trigger('to.owl.carousel', [e.item.index, duration, true]);
						}
					});
					
					thumbs.on('click', '.owl-item', function() {
						var target = $(this).index();
						$('.product-images').trigger('to.owl.carousel', [target, duration, true]);
					});
				});
			}
		},
		toggle: {
			selector: '.toggle .title',
			init: function() {
				var base = this,
				container = $(base.selector);
				container.each(function() {
					var that = $(this);
					that.on('click', function() {
					
						if (that.hasClass('toggled')) {
							that.removeClass("toggled").closest('.toggle').find('.inner').slideUp(200);
						} else {
							that.addClass("toggled").closest('.toggle').find('.inner').slideDown(200);
						}
						
					});
				});
			}
		},
		masonry: {
			selector: '.masonry:not(.posts)',
			init: function() {
				var base = this,
				container = $(base.selector);
								
				container.each(function() {
					var that = $(this),
						fitwidth = that.hasClass('blog-section') ? 0 : 1;
					
					win.load(function() {
						that.isotope({
							itemSelector : '.item',
							transitionDuration : '0.5s',
							masonry: {
								columnWidth: '.item',
								isFitWidth: fitwidth
							}
						});
						
						that.isotope( 'on', 'layoutComplete', function() {
							SITE.carousel.init();
							SITE.magnificImage.init();
						});
					});
				});
			}
		},
		grid: {
			selector: '.grid',
			init: function() {
				var base = this,
				container = $(base.selector);
								
				container.each(function() {
					var that = $(this);
					
					
					win.load(function() {
						that.isotope({
							masonry: {
								columnWidth: '.grid-sizer'
							},
							itemSelector : '.item',
							transitionDuration : '0.5s'
						});
					});
				});
			}
		},
		infiniteScroll: {
			selector: '#infinitescroll',
			init: function() {
				var base = this,
					container = $(base.selector),
					loading = container.data('loading'),
					nomore = container.data('nomore'),
					count = container.data('count'),
					total = container.data('total'),
					style = container.data('type'),
					page = 2;
				
				var scrollFunction = function(){
					if (win.scrollTop() >= $doc.height() - win.height() - 60) {
						win.unbind("scroll");
						$.post( themeajax.url, { 
							action: 'thb_ajax',
							count : count,
							page : page,
							style : style
						}, function(data){
							
							var d = $.parseHTML(data),
									l = ($(d).length - 1) / 2;
									
							if (page > total) {
								return false;
							} else {
								page++;
								$(d).appendTo(container).hide().imagesLoaded(function() {
									$(d).show();
									if (container.hasClass('masonry')) {
										container.isotope( 'appended', $(d) );
										container.isotope('layout');
									} else {
										SITE.carousel.init();
										SITE.magnificImage.init();
									}
								});
								win.scroll(scrollFunction);
							}
							
						});
					}
				};
				win.scroll(scrollFunction);
			}
		},
		shareThisArticle: {
			selector: '#product_share',
			init: function() {
				var base = this,
						container = $(base.selector),
						fb = container.data('fb'),
						tw = container.data('tw'),
						pi = container.data('pi'),
						li  = container.data('li'),
						gp  = container.data('gp'),
						boxed = container.data('boxed'),
						temp = '';
				
				if (fb) {
					temp += '<a href="#" class="'+(boxed ? 'boxed-icon ' : '')+'facebook"><i class="fa fa-facebook"></i></a> ';
				}
				if (tw) {
					temp += '<a href="#" class="'+(boxed ? 'boxed-icon ' : '')+'twitter"><i class="fa fa-twitter"></i></a> ';
				}
				if (pi) {
					temp += '<a href="#" class="'+(boxed ? 'boxed-icon ' : '')+'pinterest"><i class="fa fa-pinterest"></i></a> ';
				}
				if (li) {
					temp += '<a href="#" class="'+(boxed ? 'boxed-icon ' : '')+'linkedin"><i class="fa fa-linkedin"></i></a> ';
				}
				if (gp) {
					temp += '<a href="#" class="'+(boxed ? 'boxed-icon ' : '')+'google-plus"><i class="fa fa-google-plus"></i></a> ';
				}
				container.find('.placeholder').sharrre({
					share: {
						facebook: fb,
						twitter: tw,
						pinterest: pi,
						linkedin: li
					},
					urlCurl: $('body').data('sharrreurl'),
					template: temp,
					enableHover: false,
					enableTracking: false,
					render: function(api){
						$(api.element).on('click', '.twitter', function() {
							api.openPopup('twitter');
						});
						$(api.element).on('click', '.facebook', function() {
							api.openPopup('facebook');
						});
						$(api.element).on('click', '.pinterest', function() {
							api.openPopup('pinterest');
						});
						$(api.element).on('click', '.linkedin', function() {
							api.openPopup('linkedin');
						});
						$(api.element).on('click', '.google-plus', function() {
							api.openPopup('googlePlus');
						});
					}
				});
			}
		},
		parallax: {
			selector: '.parallax_bg',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				container.each(function() {
					
					var that = $(this),
						speed = that.data('parallax-speed'),
						direction = that.data('parallax-direction'),
						off = that.offset().top;
					
					function backgroundAnimate() {
						var top = (win.width() > 767) ? off - win.scrollTop() : 0,
							yPos = Math.floor(top / speed);
							
						if (direction === 'down') { yPos = Math.abs(yPos); }
						
						if (win.width() > 767) {
							that[0].style.setProperty( 'background-position', '50% '+ yPos + 'px', 'important' );
						}
						window.requestAnimationFrame(backgroundAnimate);
					}
					window.requestAnimationFrame(backgroundAnimate);
					
					if (direction === 'down') { speed = -speed; }
				});
			}
		},
		customScroll: {
			selector: '.no-touch .custom_scroll',
			init: function() {
				var base = this,
					container = $(base.selector);
				
				container.each(function() {
					var that = $(this);
					that.perfectScrollbar({
						wheelPropagation: false,
						suppressScrollX: true,
						scrollYMarginOffset: 10,
						scrollXMarginOffset: 10
					});
				});
				
				win.resize(function() {
					if (win.width() > 767) {
						base.resize(container);
					} else {
						
					}
				});
			},
			resize: function(container) {
				container.perfectScrollbar('update');
			}
		},
		wpml: {
			selector: '#thb_language_selector',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				container.on('change', function () {
				var url = $(this).val(); // get selected value
					if (url) { // require a URL
						window.location = url; // redirect
					}
					return false;
				});
			}
		},
		shop: {
			selector: '.products .product',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				container.each(function() {
					var that = $(this);
					
					that
					.find('.add_to_cart_button').on('click', function() {
						if ($(this).data('added-text') !== '') {
							$(this).text($(this).data('added-text'));
						}
					});
					
				}); // each
	
			}
		},
		variations: {
			selector: '.variations_form input[name=variation_id]',
			init: function() {
				var base = this,
					container = $(base.selector),
					org = $('.single-price.single_variation').html();
				
				container.on('change', function() {
					var that = $(this),
						val = that.val(),
						phtml,
						images = $('#product-images'),
						owl = images.data('owlCarousel');

					setTimeout(function(){
						if (val) {
							phtml = that.parents('.variations_form').find('.single_variation span.price').html();
						} else {
							phtml = org;	
						}
						$('.price.single_variation').html(phtml);
					}, 100);
					
					if ($('select[name=attribute_pa_color]').length) {
						var v = $('select[name=attribute_pa_color] option:selected').val(),
							i = images.find('[data-variation-color="'+v+'"]').parents('.owl-item').index();

						owl.to(i);
					}
				});
			}
		},
		reviews: {
			selector: '#comment_popup',
			init: function() {
				var base = this,
						container = $(base.selector);

				container.on( 'click', 'p.stars a', function(){
					var that = $(this);
					
					setTimeout(function(){ that.prevAll().addClass('active'); }, 10);
				});
			}
		},
		checkout: {
			selector: '.woocommerce-checkout',
			init: function() {
				
				$('#shippingsteps a').on('click', function() {
					var that = $(this),
							target = (that.data('target') ? $('#'+that.data('target')) : false);

					if (target) {
						$('#shippingsteps li').removeClass('active');
						that.parents('li').addClass('active');
						$('.section').hide();
						target.show();
						SITE.magnificInline.init();
					}
					return false;
				});
				
				$('#createaccount', '#checkout_login').on('click', function() {
					$('#checkout_register', '#checkout_login').slideToggle();
					return false;
				});
				$('#guestcheckout', '#checkout_login').on('click', function() {
					$('#shippingsteps a').eq(1).trigger('click');
					return false;
				});
				$('.continue_shipping').on('click', function() {
					$('form.checkout .billing').find('.input-text, select').trigger('change');
					if ($('form.checkout .shipping_address').is(':visible')) { $('form.checkout .shipping_address').find('.input-text, select').trigger('change'); }
					if ($('form.checkout').find('.woocommerce-invalid-required-field').length === 0) {
						$('#shippingsteps a').eq(2).trigger('click');
					}
					SITE.magnificInline.init();
					
					return false;
				});
				$('#ship-to-different-address-checkbox').on('change', function() {
					$('.shipping_address').slideToggle('slow', function() {
						if($('.shipping_address').is(':hidden')) {
							$('form.checkout .shipping_address').find('p.form-row').removeClass('woocommerce-invalid-required-field');
						}
					});
					return false;
				});
				
				$('#have_coupon').live('click', function() {
					$('.coupon-container.margin').slideToggle();
					return false;
				});
			}
		},
		myaccount: {
			selector: '#my-account-main',
			init: function() {
				var base = this,
						container = $(base.selector),
						tabs = $('.tab-pane, #my-account-main');
				container.find('.account-icon-box').on('click', function() {
					var that = $(this),
						target = $(that.attr('href'));
					
					container.hide(0, function() {
						target.fadeIn();
					});

					return false;
				});
				$('.back_to_account').on('click', function() {
					tabs.hide(0, function() {
						container.fadeIn();
					});
					return false;
				});
				$('#changepassword_btn').on('click', function() {
					$('#changeit').trigger('click');
					
					return false;
				});
			}
		},
		magnificImage: {
			selector: '[rel="magnific"], .wp-caption a',
			init: function() {
				var base = this,
						container = $(base.selector),
						stype;
				
				container.each(function() {
					if ($(this).hasClass('video')) {
						stype = 'iframe';
					} else {
						stype = 'image';
					}
					$(this).magnificPopup({
						type: stype,
						closeOnContentClick: true,
						fixedContentPos: true,
						closeBtnInside: false,
						closeMarkup: '<button title="%title%" class="mfp-close"></button>',
						mainClass: 'mfp',
						removalDelay: 250,
						overflowY: 'scroll',
						image: {
							verticalFit: false
						}
					});
				});
	
			}
		},
		magnificInline: {
			selector: '[rel="inline"]',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				container.each(function() {
					var eclass = ($(this).data('class') ? $(this).data('class') : '');

					$(this).magnificPopup({
						type:'inline',
						midClick: true,
						mainClass: 'mfp ' + eclass,
						removalDelay: 250,
						closeBtnInside: true,
						overflowY: 'scroll',
						closeMarkup: '<button title="%title%" class="mfp-close"></button>'
					});
				});
	
			}
		},
		magnificGallery: {
			selector: '[rel="gallery"]',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				container.each(function() {
					$(this).magnificPopup({
						delegate: 'a',
						type: 'image',
						closeOnContentClick: true,
						fixedContentPos: true,
						mainClass: 'mfp',
						removalDelay: 250,
						closeBtnInside: false,
						overflowY: 'scroll',
						gallery: {
							enabled: true,
							navigateByImgClick: false,
							preload: [0,1] // Will preload 0 - before current, and 1 after the current image
						},
						image: {
							verticalFit: false,
							titleSrc: function(item) {
								return item.el.attr('title');
							}
						}
					});
				});
				
			}
		},
		magnificAuto: {
			selector: '[rel="inline-auto"]',
			init: function() {
				var base = this,
					container = $(base.selector),
					eclass = (container.data('class') ? container.data('class') : ''),
					target = '#'+container.attr('id'),
					interval = container.data('interval'),
					cookie = $.cookie('north-newsletter');
				
				
				if (eclass === 'newsletter-popup') {
					if ($.cookie && !cookie) {
						
						if (interval !== '0') {
							$.cookie('north-newsletter', 1, { expires: interval, path: '/' });
						} else {
							$.removeCookie('north-newsletter');	
						}
						$.magnificPopup.open({
							type:'inline',
							items: {
								src: target,
								type: 'inline'
							},
							midClick: true,
							mainClass: 'mfp ' + eclass,
							removalDelay: 250,
							closeBtnInside: true,
							overflowY: 'scroll',
							closeMarkup: '<button title="%title%" class="mfp-close"></button>'
						});
					}
				} else {
					$.magnificPopup.open({
						type:'inline',
						items: {
							src: target,
							type: 'inline'
						},
						midClick: true,
						mainClass: 'mfp ' + eclass,
						removalDelay: 250,
						closeBtnInside: true,
						overflowY: 'scroll',
						closeMarkup: '<button title="%title%" class="mfp-close"></button>'
					});
				}
				
			}
		},
		newsletterForm: {
			selector: '#newsletter-form',
			init: function() {
				var base = this,
						container = $(base.selector),
						url = container.data('target');
				
				container.submit(function() {
					container.find('.result').load(url, {email: $('#widget_subscribe').val()},
					function() {
						$(this).fadeIn(200).delay(3000).fadeOut(200);
					});
					return false;
				});
				
			}
		},
		upSells: {
			selector: '#upsell-trigger',
			init: function() {
				var base = this,
					container = $(base.selector);
				
				if ($('.product-information .notification-box.success').length) {
					
					$('#upsell-popup').imagesLoaded(function() {
						container.trigger('click');
					});
				}
				
			}
		},
		shopSidebar: {
			selector: '.woo.sidebar .widget.woocommerce',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				container.each(function() {
					var that = $(this),
							t = that.find('>h6');
					
					t.append($('<span/>')).on('click', function() {
						t.toggleClass('active');
						t.next().animate({
							height: "toggle",
							opacity: "toggle"
						}, 300);
						$('.woo.sidebar').find('.custom_scroll').perfectScrollbar('update');
					});
				});
			}
		},
		parsley: {
			selector: '.comment-form, .wpcf7-form',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				if ($.fn.parsley) {
					container.parsley();
				}
			}
		},
		commentToggle: {
			selector: '#commenttoggle',
			init: function() {
				var base = this,
						container = $(base.selector),
						respond = $('#respond'),
						parent = respond.find('#comment_parent');
				
				container.on('click', function() {
					respond.slideToggle();
					return false;
				});
			}	
		},
		page_scroll: {
			selector: '.page_scroll',
			init: function() {
				var base = this,
						container = $(base.selector),
						nav = $('.header nav');
				
				nav.onePageNav({
					currentClass: 'current-menu-item',
					changeHash: false,
					topOffset: 100,
					scrollSpeed: 750
				});
			}	
		},
		colorCheck: {
			selector: '.header',
			check: function() {
				var base = this,
						container = $(base.selector),
						body = $('body'),
						pagi = $('.onepage-pagination');
				
				if (container.hasClass("background--light")) {
					body.add(pagi).addClass("background--light").removeClass("background--dark");
				} else if (container.hasClass("background--dark")) {
					body.add(pagi).addClass("background--dark").removeClass("background--light");
				}
			}
		},
		revslider: {
			selector: '#home-slider',
			init: function() {
				var base = this,
						container = $(base.selector),
						revid = "revapi" + $('body').data('revslider'),
						node;
				
				if ($('body').data('revslider')) {
					window[revid].bind("revolution.slide.onloaded",function (e) {
						BackgroundCheck.init({
							targets: '.header',
							images: '.tp-bgimg',
							minComplexity: 80,
							maxDuration: 1500,
							minOverlap: 0
						});
						SITE.colorCheck.check();
					});
					window[revid].bind("revolution.slide.onchange",function (e,data) {
						node = '.rev_slider ul li:nth-child('+data.slideIndex+') .tp-bgimg';
					});
					window[revid].bind("revolution.slide.onafterswap, revolution.slide.onafterswap",function (e,data) {
						BackgroundCheck.set('images', node);
						SITE.colorCheck.check();
					});
				}
			}
		},
		snap_scroll: {
			selector: '.snap_scroll',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				SITE.snap_scroll.setHeight();
				container.imagesLoaded(function() {
					container
						.addClass('loaded')
						.find('>.row').each(function() {
								var that = $(this);

								that.removeClass('row').find('>.columns').wrapAll('<div class="row"></div>');
							}).end()
						.onepage_scroll({
							sectionContainer: '.snap_scroll>.vc_row-fluid',
							animationTime: 1000,
							pagination: true,
							loop: false,
							keyboard: true,
							responsiveFallback: 768,
							afterMove: function(index) {
								SITE.animation.control();
								setTimeout(function() {
									BackgroundCheck.refresh();
									SITE.colorCheck.check();
								}, 1050);
							},
						});
					setTimeout(function() {
						SITE.animation.control();
						BackgroundCheck.init({
							targets: '.header',
							images: '.snap_scroll .section',
							minComplexity: 80,
							maxDuration: 1250,
							threshold: 50,
							minOverlap: 10
						});
						SITE.colorCheck.check();
					}, 500);
				});
				
			},
			setHeight: function() {
				var base = this,
					container = $(base.selector),
					children = container.find('.section>.row');
				container.add(children).height(win.height());
			}
		},
		contact: {
			selector: '.contact_map',
			init: function() {
				var base = this,
					container = $(base.selector);
				
				container.each(function() {
					var that = $(this),
						mapzoom = that.data('map-zoom'),
						maplat = that.data('map-center-lat'),
						maplong = that.data('map-center-long'),
						pinlatlong = that.data('latlong'),
						pinimage = that.data('pin-image'),
						style = that.data('map-style'),
						mapstyle;
						
						switch(style) {
							case 0:
								break;
							case 1:
								mapstyle = [{"featureType":"administrative","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"simplified"}]},{"featureType":"road","stylers":[{"visibility":"simplified"}]},{"featureType":"water","stylers":[{"visibility":"simplified"}]},{"featureType":"transit","stylers":[{"visibility":"simplified"}]},{"featureType":"landscape","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"visibility":"off"}]},{"featureType":"road.local","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"color":"#5f94ff"},{"lightness":26},{"gamma":5.86}]},{},{"featureType":"road.highway","stylers":[{"weight":0.6},{"saturation":-85},{"lightness":61}]},{"featureType":"road"},{},{"featureType":"landscape","stylers":[{"hue":"#0066ff"},{"saturation":74},{"lightness":100}]}];
								break;
							case 2:
								mapstyle = [{"featureType":"water","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":-78},{"lightness":67},{"visibility":"simplified"}]},{"featureType":"landscape","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"hue":"#e9ebed"},{"saturation":-90},{"lightness":-8},{"visibility":"simplified"}]},{"featureType":"transit","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":10},{"lightness":69},{"visibility":"on"}]},{"featureType":"administrative.locality","elementType":"all","stylers":[{"hue":"#2c2e33"},{"saturation":7},{"lightness":19},{"visibility":"on"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":-2},{"visibility":"simplified"}]}];
								break;
							case 3:
								mapstyle = [{"featureType":"poi","stylers":[{"visibility":"off"}]},{"stylers":[{"saturation":-70},{"lightness":37},{"gamma":1.15}]},{"elementType":"labels","stylers":[{"gamma":0.26},{"visibility":"off"}]},{"featureType":"road","stylers":[{"lightness":0},{"saturation":0},{"hue":"#ffffff"},{"gamma":0}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"lightness":50},{"saturation":0},{"hue":"#ffffff"}]},{"featureType":"administrative.province","stylers":[{"visibility":"on"},{"lightness":-50}]},{"featureType":"administrative.province","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"labels.text","stylers":[{"lightness":20}]}];
								break;
							case 4:
								mapstyle = [{"featureType":"landscape","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"stylers":[{"hue":"#00aaff"},{"saturation":-100},{"gamma":2.15},{"lightness":12}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"lightness":24}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":57}]}];
								break;
							case 5:
								mapstyle = [{"featureType":"landscape","stylers":[{"hue":"#F1FF00"},{"saturation":-27.4},{"lightness":9.4},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#0099FF"},{"saturation":-20},{"lightness":36.4},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#00FF4F"},{"saturation":0},{"lightness":0},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#FFB300"},{"saturation":-38},{"lightness":11.2},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#00B6FF"},{"saturation":4.2},{"lightness":-63.4},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#9FFF00"},{"saturation":0},{"lightness":0},{"gamma":1}]}];
								break;
							case 6:
								mapstyle = [{"stylers":[{"hue":"#2c3e50"},{"saturation":250}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":50},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]}];
								break;
							case 7:
								mapstyle = [{"stylers":[{"hue":"#16a085"},{"saturation":0}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]}];
								break;
						}
					
					var centerlatLng = new google.maps.LatLng(maplat,maplong);
					
					var mapOptions = {
						center: centerlatLng,
						styles: mapstyle,
						zoom: mapzoom,
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						scrollwheel: false,
						panControl: false,
						zoomControl: false,
						mapTypeControl: false,
						scaleControl: false,
						streetViewControl: false
					};
					
					var map = new google.maps.Map(document.getElementById("contact-map"), mapOptions);
					
					google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
						if(pinimage.length > 0) {
							var pinimageLoad = new Image();
							pinimageLoad.src = pinimage;
							
							$(pinimageLoad).load(function(){
								base.setMarkers(map, pinlatlong, pinimage);
							});
						}
						else {
							base.setMarkers(map, pinlatlong, pinimage);
						}
					});
				});
			},
			setMarkers: function(map, pinlatlong, pinimage) {
				var infoWindows = [];
				
				function showPin (i) {
					var latlong_array = pinlatlong[i].lat_long.split(','),
						marker = new google.maps.Marker({
							position: new google.maps.LatLng(latlong_array[0],latlong_array[1]),
							map: map,
							animation: google.maps.Animation.DROP,
							icon: pinimage,
							optimized: false
						}),
						contentString = '<div class="marker-info-win">'+
						'<img src="'+pinlatlong[i].image+'" class="image" />' +
						'<div class="marker-inner-win">'+
						'<h1 class="marker-heading">'+pinlatlong[i].title+'</h1>'+
						'<p>'+pinlatlong[i].information+'</p>'+ 
						'</div></div>';
					
					// info windows 
					var infowindow = new InfoBox({
							alignBottom: true,
							content: contentString,
							disableAutoPan: false,
							maxWidth: 380,
							closeBoxMargin: "10px 10px 10px 10px",
							closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
							pixelOffset: new google.maps.Size(-195, -43),
							zIndex: null,
							infoBoxClearance: new google.maps.Size(1, 1)
					});
					infoWindows.push(infowindow);
					
					google.maps.event.addListener(marker, 'click', (function(marker, i) {
						return function() {
							infoWindows[i].open(map, this);
						};
					})(marker, i));
				}
				
				for (var i = 0; i + 1 <= pinlatlong.length; i++) {  
					setTimeout(showPin, i * 250, i);
				}
			}
		},
		footerProducts: {
			selector: '#footer',
			init: function() {
				var base = this,
					container = $(base.selector),
					footer = $('#footer'),
					wrapper = $('#wrapper'),
					cc = $('.click-capture'),
					products = $('#footer_products'),
					section = products.find('.carousel-container'),
					links = $('#footer_tabs').find('a');
				
				$('#footer-toggle').on('click', function() {
					footer.toggleClass('active');
					wrapper.toggleClass('open-footer');
					return false;
				});
				
				links.on('click', function() {
					var that = $(this),
						type = that.data('type');
					
					if (!that.hasClass('active')) {
						links.removeClass('active');
						that.addClass('active');
						section.addClass('loading').height(section.outerHeight());
						
						$.post( themeajax.url, { 
						
								action: 'thb_product_ajax',
								type : type
								
						}, function(data){
							
							var d = $.parseHTML(data);
							
							$(d).imagesLoaded(function() {
								section.html(d);
								SITE.carousel.init();
								section.removeClass('loading');
							});
							
							
						});
					}
					return false;
				});
				
				cc.on('click', function() {
					wrapper.removeClass('open-footer');
					footer.removeClass('active');
				});
			}
		},
		equalHeights: {
			selector: '[data-equal]',
			init: function() {
				var base = this,
						container = $(base.selector);
				container.each(function(){
					var that = $(this),
							children = that.data("equal");
							
					that.imagesLoaded(function() {
						that.find(children).matchHeight(true);
					});
					 
				});
				
				$('.shipping-calculator-button').on('click', function() {
					setTimeout(function () {
						base.init();
					}, 800);
				});
			}
		},
		favicon: {
			selector: 'body',
			init: function() {
				var base = this,
						container = $(base.selector),
						count = container.data('cart-count');
					favicon = new Favico({
							bgColor : '#e25842',
							textColor : '#fff'
					});
				favicon.badge(count);
			}
		},
		animation: {
			selector: '#content-container .animation',
			init: function() {
				var base = this,
						container = $(base.selector);
				
				base.control(container);
				
				win.scroll(function(){
					base.control(container);
				});
			},
			control: function(element) {
				var t = -1,
					snap = $(SITE.snap_scroll.selector);

				if (snap.length > 0) {
					snap.find('.section.active').find('.animation').each(function () {

						var that = $(this);
							t++;
						setTimeout(function () {
							that.addClass("animate");
						}, 200 * t);
					});
				} else {
					element.filter(':in-viewport').each(function () {
						var that = $(this);
							t++;
						
						setTimeout(function () {
							that.addClass("animate");
						}, 200 * t);
						
					});
				}
			}
		},
		styleSwitcher: {
			selector: '#style-switcher',
			init: function() {
				var base = this,
						container = $(base.selector),
						toggle = container.find('.style-toggle'),
						onoffswitch = container.find('.switch');
				
						toggle.on('click', function() {
							container.add($(this)).toggleClass('active');
							return false;
						});
						
						onoffswitch.each(function() {
							var that = $(this);
									
							that.find('a').on('click', function() {
								var dataclass = $(this).data('class');
								
								that.find('a').removeClass('active');
								$(this).addClass('active');
								
								if ($(this).parents('ul').data('name') === 'boxed') {
									$(document.body).removeClass('boxed');
									$(document.body).addClass(dataclass);
								}
								if ($(this).parents('ul').data('name') === 'header_grid') {
									$('.header .row, #subheader .row').removeClass('notgrid');
									$('.header .row, #subheader .row').addClass(dataclass);
								}
								return false;
							});
						});
				
				var style = $('<style type="text/css" id="theme_color" />').appendTo('head');
				container.find('.first').minicolors({
					defaultValue: $('.first').data('default'),
					change: function(hex) {
						style.html('.badge.onsale, .price_slider .ui-slider-range { background:'+hex+'; } .product-category > a:after { border-color: '+hex+'; } a:hover, #nav .sf-menu > li > a:hover, .post .post-meta ul li a, .post .post-title a:hover, .more-link, #comments ol.commentlist .comment-reply-link, .price ins, .price > .amount, .product_meta p a, .shopping_bag tbody tr td.order-status.approved, .shopping_bag tbody tr td.product-name .posted_in a, .shopping_bag tbody tr td.product-quantity .wishlist-in-stock, .lost_password, #my-account-main .account-icon-box:hover, .lookbook-container .look .info a .amount { color: '+hex+'; }');	
					}
				});
			}
		}
	};
	
	// on Resize & Scroll
	win.resize(function() {
		SITE.navDropdown.init();
		SITE.snap_scroll.setHeight();
	});
	win.scroll(function(){
	});
	
	$doc.ready(function() {
		SITE.init();
	});

})(jQuery, this);