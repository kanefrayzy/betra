/** *************Init JS*********************

    TABLE OF CONTENTS
	---------------------------
	1.Ready function
	2.Load function
	3.Full height function
	4.admintres function
	5.Chat App function
	6.Resize function
 ** ***************************************/

 "use strict";
/*****Ready function start*****/
        var currencySymbols = {
            1: 'USD', 
            2: 'RUB',
            3: 'AZN',
            4: 'TRY',
			5: 'KZT'
        };
$(document).ready(function(){
	admintres();
	$('.preloader-it > .la-anim-1').addClass('la-animate');
});
/*****Ready function end*****/

$(document).ready(function() {
	"use strict";
	
	// Настройка CSRF токена для всех AJAX запросов
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	// Общая конфигурация языка для всех таблиц
	const datatableLanguage = {
		"processing": '<div class="spinner-container"><div class="spinner"></div><p>Загрузка...</p></div>',
		"search": "",
		"searchPlaceholder": "Поиск...",
		"lengthMenu": "Показать _MENU_",
		"info": "Показано _START_-_END_ из _TOTAL_",
		"infoEmpty": "Нет записей",
		"infoFiltered": "(из _MAX_)",
		"loadingRecords": "Загрузка...",
		"zeroRecords": "Ничего не найдено",
		"emptyTable": "Нет данных",
		"paginate": {
			"first": "««",
			"previous": "‹",
			"next": "›",
			"last": "»»"
		}
	};
	
	// Общие настройки для оптимизации производительности
	const commonSettings = {
		"pageLength": 25,
		"lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
		"deferRender": true, // Отложенная отрисовка для ускорения
		"dom": '<"datatable-header"<"flex justify-between items-center mb-4"<"length-menu"l><"search-box"f>>>rt<"datatable-footer"<"flex justify-between items-center mt-4"<"info-text"i><"pagination-box"p>>>',
		"language": datatableLanguage,
		"drawCallback": function() {
			// Применяем современные стили к элементам после отрисовки
			applyModernStyles(this);
		}
	};
	
	// Функция применения современных стилей
	function applyModernStyles(table) {
		const wrapper = $(table).closest('.dataTables_wrapper');
		
		// Стилизация поиска
		wrapper.find('.dataTables_filter input').attr('placeholder', 'Поиск...').addClass('modern-search-input');
		
		// Стилизация select
		wrapper.find('.dataTables_length select').addClass('modern-select');
		
		// Стилизация пагинации
		wrapper.find('.dataTables_paginate .paginate_button').addClass('modern-page-btn');
	}
	
	$('#datable_users').DataTable({
		...commonSettings,
		processing: true,
		serverSide: true,
		"pageLength": 50, // Увеличиваем для пользователей
		"deferRender": true,
		"stateSave": true, // Сохраняем состояние таблицы
		"stateDuration": 60 * 60 * 24, // 24 часа
		ajax: {
			url: `/${cpBaseUrl}/usersAjax`,
			type: "POST",
			data: function(d) {
				// Оптимизация: отправляем только необходимые данные
				return {
					draw: d.draw,
					start: d.start,
					length: d.length,
					search: d.search,
					order: d.order,
					columns: d.columns
				};
			},
			error: function(xhr, error, code) {
				console.error('DataTable Ajax Error:', {
					status: xhr.status,
					statusText: xhr.statusText,
					responseText: xhr.responseText,
					error: error,
					code: code
				});
				
				// Показываем красивое уведомление об ошибке
				const errorMsg = xhr.status === 419 ? 
					'Сессия истекла. Пожалуйста, обновите страницу.' : 
					'Ошибка загрузки данных. Код: ' + xhr.status;
				
				showDataTableError(errorMsg);
			}
		},
        columns: [
            { data: "id", searchable: true, width: "60px" },
            { data: "username", visible: false, searchable: true },
            { 
				data: "username", 
				searchable: false,
				width: "200px",
                render: function (data, type, row) {
					// Современный дизайн с аватаром
                    return `
						<div class="flex items-center gap-3">
							<img src="${row.avatar}" class="w-10 h-10 rounded-full border-2 border-blue-500" alt="${row.username}">
							<span class="font-semibold text-gray-900 dark:text-white">${row.username}</span>
						</div>
					`;
                }
            },
            { 
				data: 'balance', 
				searchable: false,
				width: "120px",
                render: function (data, type, row) {
                    var symbol = currencySymbols[row.currency_id] || '';
                    return `<span class="text-green-600 dark:text-green-400 font-bold">${row.balance} ${symbol}</span>`;
                }
            },
            { 
				data: "rank", 
				searchable: false, 
				orderable: true,
				width: "80px",
                render: function (data, type, row) {
					const rankNum = row.rank_id - 1;
                    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">${rankNum}</span>`;
                }
            },
            { 
				data: "status", 
				searchable: false, 
				orderable: true,
				width: "120px",
                render: function (data, type, row) {
					let badge = '';
                    if(row.is_admin) {
						badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">👑 Admin</span>';
					} else if(row.is_moder) {
						badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200">⚡ Moderator</span>';
					} else if(row.is_youtuber) {
						badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-200">📺 YouTuber</span>';
					} else {
						badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">👤 User</span>';
					}
                    return badge;
                }
            },
            { 
				data: "ip", 
				searchable: true, 
				orderable: false,
				width: "130px",
                render: function (data, type, row) {
                    return `<code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-600 dark:text-gray-400">${row.ip}</code>`;
                }
            },
            { 
				data: "ban", 
				searchable: false, 
				orderable: true,
				width: "80px",
                render: function (data, type, row) {
                    if(row.ban) {
						return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">🚫 Да</span>';
					}
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">✓ Нет</span>';
                }
            },
            { 
				data: null, 
				searchable: false, 
				orderable: false,
				width: "150px",
                render: function (data, type, row) {
                    return `
						<div class="flex gap-2">
							<a href="/${cpBaseUrl}/user/${row.id}" 
							   class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors">
								✏️ Изменить
							</a>
							<a href="/${cpBaseUrl}/userIdAuth/${row.id}" 
							   class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-purple-600 hover:bg-purple-700 text-white transition-colors"
							   title="Войти как пользователь">
								🔐
							</a>
						</div>
					`;
                }
            }
		]
    });
	
	// Функция показа красивой ошибки
	function showDataTableError(message) {
		const errorDiv = $('<div>')
			.addClass('fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in')
			.html(`
				<div class="flex items-center gap-3">
					<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
					</svg>
					<div>
						<p class="font-semibold">Ошибка</p>
						<p class="text-sm">${message}</p>
					</div>
				</div>
			`);
		
		$('body').append(errorDiv);
		
		setTimeout(() => {
			errorDiv.fadeOut(300, function() { $(this).remove(); });
		}, 5000);
	}

    $('#datable_1').DataTable({
		...commonSettings
    });

    $('#datable_2').DataTable({
		...commonSettings
    });


});

/*****Load function start*****/
$(window).on("load",function(){
	$(".preloader-it").delay(500).fadeOut("slow");
	/*Progress Bar Animation*/
	var progressAnim = $('.progress-anim');
	if( progressAnim.length > 0 ){
		for(var i = 0; i < progressAnim.length; i++){
			var $this = $(progressAnim[i]);
			$this.waypoint(function() {
			var progressBar = $(".progress-anim .progress-bar");
			for(var i = 0; i < progressBar.length; i++){
				$this = $(progressBar[i]);
				$this.css("width", $this.attr("aria-valuenow") + "%");
			}
			}, {
			  triggerOnce: true,
			  offset: 'bottom-in-view'
			});
		}
	}
});
/*****Load function* end*****/

/***** Full height function start *****/
var setHeightWidth = function () {
	var height,width,clickAllowed;

	height = $(window).height();
	width = $(window).width();

	// flag to allow clicking
    clickAllowed = true;

	$('.full-height').css('height', (height));
	$('.page-wrapper').css('min-height', (height));

	/*Right Sidebar Scroll Start*/
	if(width<=1007){
		$('#chat_list_scroll').css('height', (height - 267));
		$('.fixed-sidebar-right .real-time-content').css('height', (height - 280));
		$('.fixed-sidebar-right .set-height-wrap').css('height', (height - 220));
		clickAllowed = true;
	}
	else {
		$('#chat_list_scroll').css('height', (height - 221));
		$('.fixed-sidebar-right .real-time-content').css('height', (height - 230));
		$('.fixed-sidebar-right .set-height-wrap').css('height', (height - 170));
		clickAllowed = false;
	}
	/*Right Sidebar Scroll End*/

	/*Vertical Tab Height Cal Start*/
	var verticalTab = $(".vertical-tab");
	if( verticalTab.length > 0 ){
		for(var i = 0; i < verticalTab.length; i++){
			var $this =$(verticalTab[i]);
			$this.find('ul.nav').css(
			  'min-height', ''
			);
			$this.find('.tab-content').css(
			  'min-height', ''
			);
			height = $this.find('ul.ver-nav-tab').height();
			$this.find('ul.nav').css(
			  'min-height', height + 40
			);
			$this.find('.tab-content').css(
			  'min-height', height + 40
			);
		}
	}
	/*Vertical Tab Height Cal End*/
};
/***** Full height function end *****/

/***** admintres function start *****/
var $wrapper = $(".wrapper");
var admintres = function(){

	/*Counter Animation*/
	var counterAnim = $('.counter-anim');
	if( counterAnim.length > 0 ){
		counterAnim.counterUp({ delay: 10,
        time: 1000});
	}

	/*Tooltip*/
	if( $('[data-toggle="tooltip"]').length > 0 )
		$('[data-toggle="tooltip"]').tooltip();

	/*Popover*/
	if( $('[data-toggle="popover"]').length > 0 )
		$('[data-toggle="popover"]').popover()


	/*Sidebar Collapse Animation*/
	var sidebarNavCollapse = $('.fixed-sidebar-left .side-nav  li .collapse');
	var sidebarNavAnchor = '.fixed-sidebar-left .side-nav  li a';
	$(document).on("click",sidebarNavAnchor,function (e) {
		if ($(this).attr('aria-expanded') === "false")
				$(this).blur();
		$(sidebarNavCollapse).not($(this).parent().parent()).collapse('hide');
	});

	/*Panel Remove*/
	$(document).on('click', '.close-panel', function (e) {
		var effect = $(this).data('effect');
			$(this).closest('.panel')[effect]();
		return false;
	});

	/*Accordion js*/
		$(document).on('show.bs.collapse', '.panel-collapse', function (e) {
		$(this).siblings('.panel-heading').addClass('activestate');
	});

	$(document).on('hide.bs.collapse', '.panel-collapse', function (e) {
		$(this).siblings('.panel-heading').removeClass('activestate');
	});

	/*Sidebar Navigation*/
	$(document).on('click', '#toggle_nav_btn,#open_right_sidebar,#setting_panel_btn', function (e) {
		$(".dropdown.open > .dropdown-toggle").dropdown("toggle");
		return false;
	});
	$(document).on('click', '#toggle_nav_btn', function (e) {
		$wrapper.removeClass('open-right-sidebar open-setting-panel').toggleClass('slide-nav-toggle');
		return false;
	});

	$(document).on('click', '#open_right_sidebar', function (e) {
		$wrapper.toggleClass('open-right-sidebar').removeClass('open-setting-panel');
		return false;

	});

	$(document).on('click','.product-carousel .owl-nav',function(e){
		return false;
	});

	$(document).on('click', 'body', function (e) {
		if($(e.target).closest('.fixed-sidebar-right,.setting-panel').length > 0) {
			return;
		}
		$('body > .wrapper').removeClass('open-right-sidebar open-setting-panel');
		return;
	});

	$(document).on('show.bs.dropdown', '.nav.navbar-right.top-nav .dropdown', function (e) {
		$wrapper.removeClass('open-right-sidebar open-setting-panel');
		return;
	});

	$(document).on('click', '#setting_panel_btn', function (e) {
		$wrapper.toggleClass('open-setting-panel').removeClass('open-right-sidebar');
		return false;
	});
	$(document).on('click', '#toggle_mobile_nav', function (e) {
		$wrapper.toggleClass('mobile-nav-open').removeClass('open-right-sidebar');
		return;
	});


	$(document).on("mouseenter mouseleave",".wrapper > .fixed-sidebar-left", function(e) {
		if (e.type == "mouseenter") {
			$wrapper.addClass("sidebar-hover");
		}
		else {
			$wrapper.removeClass("sidebar-hover");
		}
		return false;
	});

	$(document).on("mouseenter mouseleave",".wrapper > .setting-panel", function(e) {
		if (e.type == "mouseenter") {
			$wrapper.addClass("no-transition");
		}
		else {
			$wrapper.removeClass("no-transition");
		}
		return false;
	});

	/*Todo*/
	var random = Math.random();
	$(document).on("keypress","#add_todo",function (e) {
		if ((e.which == 13)&&(!$(this).val().length == 0))  {
				$('<li class="todo-item"><div class="checkbox checkbox-success"><input type="checkbox" id="checkbox'+random+'"/><label for="checkbox'+random+'">' + $('.new-todo input').val() + '</label></div></li><li><hr class="light-grey-hr"/></li>').insertAfter(".todo-list li:last-child");
				$('.new-todo input').val('');
		} else if(e.which == 13) {
			alert('Please type somthing!');
		}
		return;
	});

	/*Chat*/
	$(document).on("keypress","#input_msg_send",function (e) {
		if ((e.which == 13)&&(!$(this).val().length == 0)) {
			$('<li class="self mb-10"><div class="self-msg-wrap"><div class="msg block pull-right">' + $(this).val() + '<div class="msg-per-detail mt-5"><span class="msg-time txt-grey">3:30 pm</span></div></div></div><div class="clearfix"></div></li>').insertAfter(".fixed-sidebar-right .real-time-content  ul li:last-child");
			$(this).val('');
		} else if(e.which == 13) {
			alert('Please type somthing!');
		}
		return;
	});
	$(document).on("keypress","#input_msg_send_widget",function (e) {
		if ((e.which == 13)&&(!$(this).val().length == 0)) {
			$('<li class="self mb-10"><div class="self-msg-wrap"><div class="msg block pull-right">' + $(this).val() + '<div class="msg-per-detail mt-5"><span class="msg-time txt-grey">3:30 pm</span></div></div></div><div class="clearfix"></div></li>').insertAfter(".real-time-for-widgets .real-time-content  ul li:last-child");
			$(this).val('');
		} else if(e.which == 13) {
			alert('Please type somthing!');
		}
		return;
	});
	$(document).on("keypress","#input_msg_send_chatapp",function (e) {
		if ((e.which == 13)&&(!$(this).val().length == 0)) {
			$('<li class="self mb-10"><div class="self-msg-wrap"><div class="msg block pull-right">' + $(this).val() + '<div class="msg-per-detail mt-5"><span class="msg-time txt-grey">3:30 pm</span></div></div></div><div class="clearfix"></div></li>').insertAfter(".real-time-for-widgets-1 .real-time-content  ul li:last-child");
			$(this).val('');
		} else if(e.which == 13) {
			alert('Please type asomthing!');
		}
		return;
	});

	$(document).on("click",".fixed-sidebar-right .real-time-cmplt-wrap .real-time-data",function (e) {
		$(".fixed-sidebar-right .real-time-cmplt-wrap").addClass('chat-box-slide');
		return false;
	});
	$(document).on("click",".fixed-sidebar-right #goto_back",function (e) {
		$(".fixed-sidebar-right .real-time-cmplt-wrap").removeClass('chat-box-slide');
		return false;
	});

	/*Chat for Widgets*/
	$(document).on("click",".real-time-for-widgets.real-time-cmplt-wrap .real-time-data",function (e) {
		$(".real-time-for-widgets.real-time-cmplt-wrap").addClass('chat-box-slide');
		return false;
	});
	$(document).on("click","#goto_back_widget",function (e) {
		$(".real-time-for-widgets.real-time-cmplt-wrap").removeClass('chat-box-slide');
		return false;
	});
	/*Horizontal Nav*/
	$(document).on("show.bs.collapse",".horizontal-nav .fixed-sidebar-left .side-nav > li > ul",function (e) {

	});

	/*Slimscroll*/
	$('.nicescroll-bar').slimscroll({height:'100%',color: '#878787', disableFadeOut : true,borderRadius:0,size:'4px',alwaysVisible:false});
	$('.message-nicescroll-bar').slimscroll({height:'229px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0});
	$('.message-box-nicescroll-bar').slimscroll({height:'350px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0});
	$('.product-nicescroll-bar').slimscroll({height:'346px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0});
	$('.app-nicescroll-bar').slimscroll({height:'162px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0});
	$('.todo-box-nicescroll-bar').slimscroll({height:'365px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0});
	$('.users-nicescroll-bar').slimscroll({height:'370px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0});
	$('.users-real-time-nicescroll-bar').slimscroll({height:'257px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0});
	$('.chatapp-nicescroll-bar').slimscroll({height:'543px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0});
	$('.chatapp-real-time-nicescroll-bar').slimscroll({height:'483px',size: '4px', start: 'bottom', color: '#878787',disableFadeOut : true,borderRadius:0});

	/*Product carousel*/
	if( $('.product-carousel').length > 0 )
	var $owl = $('.product-carousel').owlCarousel({
		loop:true,
		margin:15,
		nav:true,
		navText: ["<i class='ti-angle-left'></i>","<i class='ti-angle-right'></i>"],
		dots:false,
		autoplay:true,
		responsive:{
			0:{
				items:1
			},
			400:{
				items:2
			},
			767:{
				items:3
				},
			1399:{
				items:4
			}
		}
	});

	/*Refresh Init Js*/
	var refreshMe = '.refresh';
	$(document).on("click",refreshMe,function (e) {
		var panelToRefresh = $(this).closest('.panel').find('.refresh-container');
		var dataToRefresh = $(this).closest('.panel').find('.panel-wrapper');
		var loadingAnim = panelToRefresh.find('.la-anim-1');
		panelToRefresh.show();
		setTimeout(function(){
			loadingAnim.addClass('la-animate');
		},100);
		function started(){} //function before timeout
		setTimeout(function(){
			function completed(){} //function after timeout
			panelToRefresh.fadeOut(800);
			setTimeout(function(){
				loadingAnim.removeClass('la-animate');
			},800);
		},1500);
		  return false;
	});

	/*Fullscreen Init Js*/
	$(document).on("click",".full-screen",function (e) {
		$(this).parents('.panel').toggleClass('fullscreen');
		$(window).trigger('resize');
		return false;
	});

	/*Nav Tab Responsive Js*/
	$(document).on('show.bs.tab', '.nav-tabs-responsive [data-toggle="tab"]', function(e) {
		var $target = $(e.target);
		var $tabs = $target.closest('.nav-tabs-responsive');
		var $current = $target.closest('li');
		var $parent = $current.closest('li.dropdown');
			$current = $parent.length > 0 ? $parent : $current;
		var $next = $current.next();
		var $prev = $current.prev();
		$tabs.find('>li').removeClass('next prev');
		$prev.addClass('prev');
		$next.addClass('next');
		return;
	});
};
/***** admintres function end *****/

/***** Chat App function Start *****/
var chatAppTarget = $('.real-time-for-widgets-1.real-time-cmplt-wrap');
var chatApp = function() {
	$(document).on("click",".real-time-for-widgets-1.real-time-cmplt-wrap .real-time-data",function (e) {
		var width = $(window).width();
		if(width<=1007) {
			chatAppTarget.addClass('real-time-box-slide');
		}
		return false;
	});
	$(document).on("click","#goto_back_widget_1",function (e) {
		var width = $(window).width();
		if(width<=1007) {
			chatAppTarget.removeClass('real-time-box-slide');
		}
		return false;
	});
};
/***** Chat App function End *****/

var boxLayout = function() {
	if((!$wrapper.hasClass("rtl-layout"))&&($wrapper.hasClass("box-layout")))
		$(".box-layout .fixed-sidebar-right").css({right: $wrapper.offset().left + 300});
		else if($wrapper.hasClass("box-layout rtl-layout"))
			$(".box-layout .fixed-sidebar-right").css({left: $wrapper.offset().left});
}
boxLayout();

/**Only For Setting Panel Start**/

/*Fixed Slidebar*/
var fixedHeader = function() {
	if($(".setting-panel #switch_3").is(":checked")) {
		$wrapper.addClass("scrollable-nav");
	} else {
		$wrapper.removeClass("scrollable-nav");
	}
};
fixedHeader();
$(document).on('change', '.setting-panel #switch_3', function () {
	fixedHeader();
	return false;
});

/*Theme Color Init*/
$(document).on('click', '.theme-option-wrap > li', function (e) {
	$(this).addClass('active-theme').siblings().removeClass('active-theme');
	$wrapper.removeClass (function (index, className) {
		return (className.match (/(^|\s)theme-\S+/g) || []).join(' ');
	}).addClass($(this).attr('id')+'-active');
	return false;
});

/*Topbar Color Init*/
var topColor = 'input:radio[name="radio-topbar-color"]';
if( $('input:radio[name="radio-topbar-color"]').length > 0 ){
	$(document).on('click',topColor, function (e) {
		$wrapper.removeClass (function (index, className) {
			return (className.match (/(^|\s)navbar-top-\S+/g) || []).join(' ');
		}).addClass($(this).attr('id'));
		return;
	});
}

/*Reset Init*/
$(document).on('click', '#reset_setting', function (e) {
	$('.theme-option-wrap > li').removeClass('active-theme').first().addClass('active-theme');
	$wrapper.removeClass (function (index, className) {
		return (className.match (/(^|\s)theme-\S+/g) || []).join(' ');
	}).addClass('theme-2-active');
	if($(".setting-panel #switch_3").is(":checked"))
		$('.setting-panel .layout-switcher .switchery').trigger('click');
		$('#navbar-top-light').trigger('click');
	return false;
});


/*Switchery Init*/
var elems = Array.prototype.slice.call(document.querySelectorAll('.setting-panel .js-switch'));
$('.setting-panel .js-switch').each(function() {
	new Switchery($(this)[0], $(this).data());
});

/*Only For Setting Panel end*/

/***** Resize function start *****/
$(window).on("resize", function () {
	setHeightWidth();
	boxLayout();
	chatApp();
}).resize();
/***** Resize function end *****/

