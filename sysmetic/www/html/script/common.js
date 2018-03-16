// NumberFomat (3자리마다 자릿수 , 찍어줌)
String.prototype.number_format = function(){
	return this.replace(/^(\-?)0+([1-9]+)/, '$1$2').replace(/^0+$/, '0').replace(/(\d)(?=(?:\d{3})+(?!\d))/g,'$1,');
}
Number.prototype.number_format = function(){
	return this.toString().number_format();
}
String.prototype.parse_number = function(){
	return this.replace(/[^0-9-]/g, '') ? parseInt(this.replace(/[^0-9-]/g, '')) : 0;
}
Number.prototype.parse_number = function(){
	return this.toString().parse_number();
}

var winHeight = $(window).height();
var winWindh = $(window).width();
$('document').ready(function(){


});

//layer popup
function commonLayerOpen(thisClass){
	$('.'+thisClass).fadeIn();
}
function commonLayerClose(thisClass){
	$('.'+thisClass).fadeOut();
}

// 공용알림(alert)
function notice(msg) {
	$('.common_notice .txt_info .mark').html(msg);
	commonLayerOpen('common_notice');
}

// login
function login() {

	if (!confirm("로그인이 필요합니다\n로그인 하시겠습니까?")) {
		return;
	}

	returl = '';
	if ($(location).attr('pathname') != '/') {
		var ref = $(location).attr('pathname') + $(location).attr('search');	//#returns href
		returl = "?redirect_url="+encodeURIComponent(ref);
		//alert(returl);
	}
	$(location).attr('href', "/signin" + returl);
	//document.location.href="/signin" + returl;
}

function bigData() {
	//alert('준비중입니다');
	window.open("/bigdata");
}

function mediaRoom() {
	alert('준비중입니다');
}

jQuery(function($){

	// Common
	var select_root = $('div.select');
	var select_value = $('.myValue');
	var select_a = $('div.select>ul>li>a');
	var select_input = $('div.select>ul>li>input[type=radio]');
	var select_label = $('div.select>ul>li>label');

	// Radio Default Value
	$('div.myValue').each(function(){
		var default_value = $(this).next('.iList').find('input[checked]').next('label').text();
		$(this).append(default_value);
	});

	// Line
	select_value.bind('focusin',function(){$(this).addClass('outLine');});
	select_value.bind('focusout',function(){$(this).removeClass('outLine');});
	select_input.bind('focusin',function(){$(this).parents('div.select').children('div.myValue').addClass('outLine');});
	select_input.bind('focusout',function(){$(this).parents('div.select').children('div.myValue').removeClass('outLine');});

	// Show
	function show_option(){
		$(this).parents('div.select:first').toggleClass('open');
	}

	// Hover
	function i_hover(){
		$(this).parents('ul:first').children('li').removeClass('hover');
		$(this).parents('li:first').toggleClass('hover');
	}

	// Hide
	function hide_option(){
		var t = $(this);
		setTimeout(function(){
			t.parents('div.select:first').removeClass('open');
		}, 1);
	}

	// Set Input
	function set_label(){
		var v = $(this).next('label').text();
		$(this).parents('ul:first').prev('.myValue').text('').append(v);
		$(this).parents('ul:first').prev('.myValue').addClass('selected');
	}

	// Set Anchor
	function set_anchor(){
		var v = $(this).text();
		$(this).parents('ul:first').prev('.myValue').text('').append(v);
		$(this).parents('ul:first').prev('.myValue').addClass('selected');
	}

	// Anchor Focus Out
	$('*:not("div.select a")').focus(function(){
		$('.aList').parent('.select').removeClass('open');
	});

	select_value.click(show_option);
	select_root.find('ul').css('position','absolute');
	select_root.removeClass('open');
	select_root.mouseleave(function(){$(this).removeClass('open');});
	select_a.click(set_anchor).click(hide_option).focus(i_hover).hover(i_hover);
	select_input.change(set_label).focus(set_label);
	select_label.hover(i_hover).click(hide_option);

	// Form Reset
	$('input[type="reset"], button[type="reset"]').click(function(){
		$(this).parents('form:first').find('.myValue').each(function(){
			var origin = $(this).next('ul:first').find('li:first label').text();
			$(this).text(origin).removeClass('selected');
		});
	});

	// 숫자입력에 이벤트 걸기
	jQuery('.input_number').on('focus', function(){
		if(jQuery(this).attr('readonly') != "readonly") {
			changeNumberformat(this, 'int');
			this.select();
			jQuery(this).addClass('input_number_on');
		}
	}).on('blur', function(){
		if(jQuery(this).attr('readonly') != "readonly") {
			if(this.value != '') {
				changeNumberformat(this);
			}
			jQuery(this).removeClass('input_number_on');
		}
	});

	jQuery('.input_number_only').on('keydown', function(e){
		// 숫자와 `-` 만 입력시킴 (num,arrow,bs,tab,-)
		var bCtrl = false;
		if (window.event) {
			key = window.event.keyCode;
			bCtrl = !!window.event.ctrlKey; // typecast to boolean
		} else {
			key = e.which;
			bCtrl = !!e.ctrlKey;
		}

		if( !( (e.which >= 45 && e.which <= 57) || (e.which >= 96 && e.which <= 105) || (e.which >= 35 && e.which <= 40) || e.which == 0 || e.which == 8 || e.which == 9 || e.which == 109 || e.which == 189 || e.which == 13 || e.which == 116 ) ) {
			if(!(bCtrl && (e.which == 67 || e.which == 86) )) {
				e.stopImmediatePropagation();
				return false;
			} else {
			}
		}
	});

});

// 통화용 Input 에 자릿수(,)를 찍거나 없애줌
function changeNumberformat(obj, mode)
{
	var strVal = '';
	if(mode == 'int') {
		strVal = obj.value.replace(/[^0-9-]/g, '');
		if(strVal) strVal = parseInt(strVal);
	} else {
		strVal = obj.value.number_format();
	}
	obj.value = strVal;
}

function showLayer(obj) {
	var maskHeight = screen.height;
	var maskWidth = screen.width;
	var scroll = $(window).scrollTop();
	var top = ($(window).height() / 2) - ($("#"+obj).height() / 2)+(scroll-60);
	var left = ($(window).width() / 2) - ($("#"+obj).width() / 2);

	if (screen.width < $(document).width()) {
		maskWidth = $(document).width();
	} else {
		maskWidth = screen.width;
	}

	if (screen.height < $(document).height()) {
		maskHeight = $(document).height();
	} else {
		maskHeight = screen.height;
	}

	$('#mask').css({ 'height': maskHeight });
	$("#"+obj).css({
		"position": "absolute",
		"top": 0,
		"left": 0,
		"margin-left": left,
		"margin-top": top
	});

	$('#mask').fadeIn("fast",0);
	$('#mask').fadeTo("fast", 0.4);
	$('#'+obj).fadeTo("fast", 1);
}

function showLayer2(obj) {
	var maskHeight = screen.height;
	var maskWidth = screen.width;
	var scroll = $(window).scrollTop();
	var top = ($(window).height() / 2) - (322)+(scroll-190);
	var left = ($(window).width() / 2) - ($("#"+obj).width() / 2);

	if (screen.width < $(document).width()) {
		maskWidth = $(document).width();
	} else {
		maskWidth = screen.width;
	}

	if (screen.height < $(document).height()) {
		maskHeight = $(document).height();
	} else {
		maskHeight = screen.height;
	}

	$('#mask').css({ 'height': maskHeight });
	$("#"+obj).css({
		"position": "absolute",
		"top": 0,
		"left": 0,
		"margin-left": 0,
		"margin-top": top
	});

	$('#mask').fadeIn("fast",0);
	$('#mask').fadeTo("fast", 0.4);
	$('#'+obj).fadeTo("fast", 1);
}

function btn_mv_up(oj) {
 if(!oj) return false;
 var o = $(oj);
 var p = $(window).scrollTop();
 if(p > 98){ o.fadeIn('slow'); }    // 나타나는 위치 지정
 else if(p < 400){ o.fadeOut('slow'); }    // 숨기는 위치 지정
}

$(document).scroll(function() {
  btn_mv_up('#menu_layer');
 }).on('click', '#menu_layer', function() {
  $("html, body").animate({}, 'slow');
});

function closeLayer(obj) {
	document.getElementById(obj).style.display = "none";
	document.getElementById('mask').style.display = "none";
}

function chg_select_star (obj, style) {
	document.getElementById(obj).className = style;
}

function chg_info (obj, num) {
	var List = document.getElementsByName("strategy_view");
	var List_leg = List.length;

	List[obj].style.display = "none";
	for(i = 0; i < List_leg; i++){
		if (i==num) List[i].style.display = "block";
	}
}

function chg_tab (obj, num) {
	for(i = 0; i < 3; i++)
		if (i==num) document.getElementById('service'+i).style.display = "block";
		else document.getElementById('service'+i).style.display = "none";
}

function chg_tab2 (obj, num) {
	for(i = 0; i < 4; i++)
		if (i==num) document.getElementById('guide'+i).style.display = "block";
		else document.getElementById('guide'+i).style.display = "none";
}

function chg_tab3 (obj, num) {
	for(i = 0; i < 2; i++)
		if (i==num) document.getElementById('invest'+i).style.display = "block";
		else document.getElementById('invest'+i).style.display = "none";
}

function showSearch (obj) {
	document.getElementById(obj).style.display = "block";
}

$(function() {
	if($("input.datepicker").length > 0){
		$("input.datepicker").datepicker();
	}
});


function autoResize(i)
{
    var iframeHeight= i.contentWindow.document.body.scrollHeight;
	var Obj = document.getElementById("tab_frame");
    i.height=iframeHeight+20;
}

// kkm
function comma(str) {
	str = String(str);
	return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

function uncomma(str) {
	str = String(str);
	return str.replace(/[^\d-]+/g, '');
}

function uncommadot(str) {
	str = String(str);
	return str.replace(/[^\d-\.]+/g, '');
}

function inputOnlyNumberDot(obj) {
	obj.value = uncommadot(obj.value);
}

function inputOnlyNumber(obj) {
	obj.value = uncomma(obj.value);
}

function inputNumberFormat(obj) {
	obj.value = comma(uncomma(obj.value));
}

function escapedHTML(str){
	return str.replace(/&/g, '&amp;').replace(/>/g, '&gt;').replace(/</g, '&lt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
}

function shareFB(title, url){
	/*
	var fb_url = 'http://www.facebook.com/sharer.php?u=' + encodeURIComponent(url);
	window.open(fb_url);
	*/
	FB.ui({
	  method: 'feed',
	  link: url,
	  caption: title,
	}, function(response){});
}

function fitImageSize(obj, href, maxWidth, maxHeight) {
	var image = new Image();

	image.onload = function(){

		var width = image.width;
		var height = image.height;

		var scalex = maxWidth / width;
		var scaley = maxHeight / height;

		var scale = (scalex < scaley) ? scalex : scaley;
		if (scale > 1)
			scale = 1;

		obj.width = scale * width;
		obj.height = scale * height;

		obj.style.display = "";
	}
	image.src = href;
}

// 리스트 차트
function loadGraph(el_id){
	// $.getJSON('', function (data) {
		$('#' + el_id).highcharts({
			chart: {
				// zoomType: 'x',
				animation: false,
				spacing: [0, 0, 0, 0]
			},
			title: {
				text: null
			},
			subtitle: {
				text: null
			},
			exporting: { enabled: false },
			tooltip: { enabled: false},
			xAxis: {
				labels: {
					enabled: false
				},
				type: 'datetime',
				minRange: 14 * 24 * 3600000 // fourteen days

			},
			yAxis: {
				labels: {
					enabled: false
				},
				title: {
					text: null
				}
			},
			legend: {
				enabled: false
			},
			plotOptions: {
				area: {
					fillColor: {
						linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
						stops: [
							[0, Highcharts.getOptions().colors[0]],
							[1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
						]
					},
					marker: {
						enabled: false,
						radius: 2
					},
					lineWidth: 1,
					states: {
						hover: {
							lineWidth: 1
						}
					},
					threshold: null
				},
				series:{
					allowPointSelect: false,
					enableMouseTracking:false,
					animation:false
				}
			},

			series: [{
				type: 'area',
				name: 'USD to EUR',
				pointInterval: 24 * 3600 * 1000,
				// pointStart: Date.UTC(2006, 0, 1),
				data: $('#'+el_id).data('graph-data')
			}],
			credits:{
				enabled: false
			}
		});
		$('#' + el_id).data('loaded', true);
	// });
}

function plus(el_id){
	var percent = parseInt($('#'+el_id).val().replace(/[^\d-]+/g, '')) + 1;
	if(percent > 100) percent = 100;
	$('#'+el_id).val(percent + '%');
}

function minus(el_id){
	var percent = parseInt($('#'+el_id).val().replace(/[^\d-]+/g, '')) - 1;
	if(percent < 0) percent = 0;
	$('#'+el_id).val(percent + '%');
}

function goLounge(uid) {
	location.href='/lounge/pb/'+uid;
}

function unfollow(type, id, callback) {

	if (!$('.product_unfollow').length) {

		var frm = '<article class="layer_popup product_unfollow">';
			frm+= '<div class="dim" onclick="commonLayerClose(\'product_unfollow\')"></div>';
			frm+= '<div class="contents">';
			frm+= 	'<div class="layer_header">';
			frm+= 		'<h2>안내</h2>';
			frm+= 		'<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose(\'product_unfollow\')"></button>';
			frm+= 	'</div>';
			frm+= 	'<div class="cont">';
			frm+= 		'<div class="summary">';
			frm+= 			'<p class="q_msg">';
			frm+= 				'해당 상품을<br />';
			frm+= 				'<span class="mark">Unfollow</span> 하시겠습니까?';
			frm+= 			'</p>';
			frm+= 		'</div>';
			frm+= 		'<div class="btn_area half">';
			frm+= 			'<a href="javascript:;" class="btn_common_red" id="del" onclick="commonLayerClose(\'product_unfollow\')">예</a>';
			frm+= 			'<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose(\'product_unfollow\')">아니오</a>';
			frm+= 		'</div>';
			frm+= 	'</div>';
			frm+= '</div>';
			frm+= '</article>';

		$('body').append(frm);

	}

	commonLayerOpen('product_unfollow');

	$('.product_unfollow #del').unbind('click').on('click', function() {
		$.get('/' + type + '/' + id + '/unfollow', {type:'json'}, function(data){
			if (data.result) {
				callback();
			} else {
			}
		}, 'json');
	});
}



function goTwitter(str) {
    var url = "https://twitter.com/share?" + str;
    window.open(url, 'twitter', 'width=450px, height=450px');
}

function goFacebook(str) {
    var url = "https://www.facebook.com/sharer/sharer.php?" + str;
    window.open(url, 'facebook', 'width=450px, height=450px');
}