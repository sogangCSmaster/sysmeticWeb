/**
 * jQuery Syaku Rolling ver 1.2.0
 *
 * Copyright (c) Seok Kyun. Choi. 최석균
 * GNU Lesser General Public License
 * http://www.gnu.org/licenses/lgpl.html
 *
 * registered date 20110830
 * http://syaku.tistory.com
 */

(function($) {

  $.fn.srolling = function(settings) {

    settings = jQuery.extend({
      data : [ ],
      name : '#srolling_area', // 롤링
      item_count : 1, // 아이템 움직임 수
      cache_count : 10, // 임시 아이템 수
      width : 100, // 아이템 크기
      height : 100,
      auto : false, // 자동 움직임
      delay : 1000, // 딜레이시간
      delay_frame : 500, // 흐르는 속도
      move : 'left',
      prev : '#srolling_prev',
      next : '#srolling_next'
    }, settings);

    var name = settings.name;
    var auto = settings.auto;
    var auto_go = auto;
    var delay = settings.delay;
    var delay_time = delay *2;
    var delay_frame = settings.delay_frame
    var move = settings.move;
    var prev = settings.prev;
    var next = settings.next;

    var data = settings.data;
    var item_count = settings.item_count;
    var cache_count = settings.cache_count;
    var index = settings.index;
    var item_width = settings.width;
    var item_height = settings.height;

    var item_total = data.length; // 아이템 총수
    var prev_idx = item_total;
    var next_idx = 0;

    var start_left = item_width *-2;

    var w_full_size = (parseInt(item_width) * parseInt(item_total)) * 10;

    switch (move) {
      case 'down' : 
      case 'top' : 
        w_full_size = item_width; 
        start_left = item_height * (cache_count *-1);
      break;
    }

    var box = this;
    var box_area = box.append("<div id='srolling_area' style='width:" + w_full_size + "px;position: absolute;white-space:nowrap;'></div>");
    box_area = jQuery(name,box);
    var item_area = jQuery("<div></div>").css("width",item_width).css("height",item_height).css("float","left").css("overflow","hidden");

    var sTime = null;
    var sTime2 = null;

    function _initialize() {
      _items();
      if (auto) { _play(); }

      jQuery(prev).click(function() {
        _prev_act();
      });

      jQuery(next).click(function() {
        _next_act();
      });

      jQuery(box_area).mouseover(function() {
        if (auto) {
          clearTimeout(sTime);
          auto_go = false;
        }
      });

      jQuery(box_area).mouseout(function() {
        if (auto) {
          auto_go = true;
          _play();
        }
      });

      // html5 & jquerymobile
      box_area.bind('swipeleft', function(event) {
        _next_act();
      });
      box_area.bind('swiperight', function(event) {
        _prev_act();
      });

    }

    function _start_box() {
      switch (move) {
        case 'down' : 
        case 'top' : box_area.css('top',start_left); break;
        case 'right' : 
        case 'left' : box_area.css('left',start_left); break;
      }
    }

    function _play() {
      switch (move) {
        case 'down' : 
        case 'right' : sTime = setTimeout(_prev_auto_act,delay_time); break;
        case 'top' : 
        case 'left' : sTime = setTimeout(_next_auto_act,delay_time); break;
      }
    }

    function _prev_auto_act() {
      if (auto && auto_go) {
        _prev_act();
        sTime = setTimeout(_prev_auto_act,delay_time);
      }
    }
    
    function _next_auto_act() {
      if (auto && auto_go) {
        _next_act();
        sTime =setTimeout(_next_auto_act,delay_time);
      }
    }

    function _prev_act() {
      var prev_obj = jQuery(prev);
      var next_obj = jQuery(next);

      prev_obj.unbind('click');
      next_obj.unbind('click');

      switch (move) {
        case 'left' : 
        case 'right' : 

          box_area.animate({
            left: '+=' + (item_width * item_count)
          }, delay_frame, function() { 
            for (var i = 0; i < item_count; i++) { box_area.children().last().remove(); }
            _create_prev();
            _start_box();

            prev_obj.click(_prev_act);
            next_obj.click(_next_act);

          });

        break;
        case 'top' : 
        case 'down' : 


          box_area.animate({
            top: '+=' + (item_height * item_count)
          }, delay_frame, function() { 
            for (var i = 0; i < item_count; i++) { box_area.children().last().remove(); }
            _create_prev();
            _start_box();
            prev_obj.click(_prev_act);
            next_obj.click(_next_act);
          });

        break;
      }

    }

    function _next_act() {
      var prev_obj = jQuery(prev);
      var next_obj = jQuery(next);

      prev_obj.unbind('click');
      next_obj.unbind('click');

      switch (move) {
        case 'down' : 
        case 'top' : 

          box_area.animate({
            top: '-=' + (item_height * item_count)
          }, delay_frame, function() { 
            for (var i = 0; i < item_count; i++) { box_area.children().first().remove(); }
            _create_next();
            _start_box();
            prev_obj.click(_prev_act);
            next_obj.click(_next_act);
          });

        break;

        case 'right' : 
        case 'left' : 

          box_area.animate({
            left: '-=' + (item_width * item_count)
          }, delay_frame, function() { 
            for (var i = 0; i < item_count; i++) { box_area.children().first().remove(); }
            _create_next();
            _start_box();
            next_obj.click(_next_act);
            prev_obj.click(_prev_act);
          });

        break;
      }

    }

    function _items() {
      _start_box();
      
      for (var i = cache_count; i > 0; i--) {
        if (item_total <= prev_idx || prev_idx < 0) { prev_idx = item_total-1; }
        var t_obj = item_area.clone();
        box_area.prepend( t_obj.append(jQuery(data[prev_idx])).attr('class','srolling_item_' + prev_idx) );
        prev_idx--;
      }
      
      for (var i=0; i < cache_count; i++) {
        if (item_total <= next_idx || next_idx < 0) { next_idx = 0; }
        var t_obj = item_area.clone();
        box_area.append( t_obj.append(jQuery(data[next_idx])).attr('class','srolling_item_' + next_idx) );
        next_idx++;
      }

    }

    function _create_prev() {
      var p_obj = box_area.children().first().attr('class');
      prev_idx = parseInt(p_obj.replace('srolling_item_','')) - 1;

      for (var i = item_count; i > 0; i--) {
        if (item_total <= prev_idx || prev_idx < 0) { prev_idx = (item_total-1); }

        var t_obj = item_area.clone();
        box_area.prepend( t_obj.append(jQuery(data[prev_idx])).attr('class','srolling_item_' + prev_idx) );
        prev_idx--;
      }
    }

    function _create_next() {
      var n_obj = box_area.children().last().attr('class');
      next_idx = parseInt(n_obj.replace('srolling_item_','')) + 1;

      for (var i=0; i < item_count; i++) {
        if (item_total <= next_idx || next_idx < 0) { next_idx = 0; }

        var t_obj = item_area.clone();
        box_area.append( t_obj.append(jQuery(data[next_idx])).attr('class','srolling_item_' + next_idx) );
        next_idx++;
      }
    }

    _initialize();
  };
  
})(jQuery);

