$(document).ready(function(){
	// === Sidebar navigation === //

	$('.submenu > a').click(function(e)
	{
		e.preventDefault();
		var submenu = $(this).siblings('ul');
		var li = $(this).parents('li');
		var submenus = $('#sidebar li.submenu ul');
		var submenus_parents = $('#sidebar li.submenu');
		if(li.hasClass('open'))
		{
			if(($(window).width() > 768) || ($(window).width() < 479)) {
				submenu.slideUp();
			} else {
				submenu.fadeOut(250);
			}
			li.removeClass('open');
		} else
		{
			if(($(window).width() > 768) || ($(window).width() < 479)) {
				submenus.slideUp();
				submenu.slideDown();
			} else {
				submenus.fadeOut(250);
				submenu.fadeIn(250);
			}
			submenus_parents.removeClass('open');
			li.addClass('open');
		}
	});

	var ul = $('#sidebar > ul');

	$('#sidebar > a').click(function(e)
	{
		e.preventDefault();
		var sidebar = $('#sidebar');
		if(sidebar.hasClass('open'))
		{
			sidebar.removeClass('open');
			ul.slideUp(250);
		} else
		{
			sidebar.addClass('open');
			ul.slideDown(250);
		}
	});

	// === Resize window related === //
	$(window).resize(function()
	{
		if($(window).width() > 479)
		{
			ul.css({'display':'block'});
			$('#content-header .btn-group').css({width:'auto'});
		}
		if($(window).width() < 479)
		{

			fix_position();
		}
		if($(window).width() > 768)
		{
			$('#user-nav > ul').css({width:'auto',margin:'0'});
            $('#content-header .btn-group').css({width:'auto'});
		}
	});

	if($(window).width() > 479)
	{
	   $('#content-header .btn-group').css({width:'auto'});
		ul.css({'display':'block'});
	}

});

//Mini scrolls
$(function(){
	$('#idk_notifications_scroll').slimScroll({
		height: '200px'
	});
});
$(function(){
	$('#idk_mail_notifications_scroll').slimScroll({
		height: '200px'
	});
});
$(function(){
	$('#idk_tasks_notifications_scroll').slimScroll({
		height: '200px'
	});
});
$(function(){
	$('.idk_events_box_top').slimScroll({
		height: '130px'
	});
});
$(function(){
	$('.idk_tasks').slimScroll({
		height: '300px'
	});
});
$(function(){
	$('.idk_todo_list').slimScroll({
		height: '300px'
	});
});
$(function(){
	$('.idk_project_list').slimScroll({
		height: '400px',
		alwaysVisible: true
	});
});


//Tooltip
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

$(function() {
	$('.matchHeight').matchHeight();
});

//Bootstrap select
(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof module === 'object' && module.exports) {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(root["jQuery"]);
  }
}(this, function (jQuery) {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Ništa izabrano',
    noneResultsText: 'No results match {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? "{0} item selected" : "{0} items selected";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'Limit reached ({n} item max)' : 'Limit reached ({n} items max)',
        (numGroup == 1) ? 'Group limit reached ({n} item max)' : 'Group limit reached ({n} items max)'
      ];
    },
    selectAllText: 'Select All',
    deselectAllText: 'Deselect All',
    multipleSeparator: ', '
  };
})(jQuery);


}));

//Input date
$(document).ready(function() {
var msg="";
var elements = document.getElementsByTagName("INPUT");

for (var i = 0; i < elements.length; i++) {
   elements[i].oninvalid =function(e) {
        if (!e.target.validity.valid) {
        switch(e.target.id){
            case 'password' :
            e.target.setCustomValidity("Bad password");break;
            case 'login_email' :
            e.target.setCustomValidity("Username cannot be blank");break;
        default : e.target.setCustomValidity("");break;

        }
       }
    };
   elements[i].oninput = function(e) {
        e.target.setCustomValidity(msg);
    };
}
})


$(document).ready(function() {
	//fancybox
	$(".fancybox").fancybox();

	//Submit disabled
	$('#idk_form').submit(function(e) {
		$("li").removeClass("hidden");
		$( "button" ).prop( "disabled", true );
	});

	//Form on leve error
	$('form').on('change keyup keydown', 'input, textarea, select', function () {
		$(this).addClass('changed-input');
	});

	$(window).on('beforeunload', function () {
		if ($('.changed-input').length) { return true; }
	});

	$("form").on("submit", function(e) {
		$(window).off("beforeunload");
		return true;
	});

	$( ".idk_todo_index" ).click(function() {
		$(window).off("beforeunload");
		return true;
	});

	//DataTable width fix
	$('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
		$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
	});

	//timeago
	timeago().render($('.timeago'));
});

$(window).load(function() {
	$("#idk_loader").fadeOut("slow");;
});
