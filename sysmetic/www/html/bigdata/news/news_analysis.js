(function($) {
  var page = 0;
  var category = '';
  var query = '';
  var lastPage = -1;
  var blocked = true;
  var offs = 170;
  var xhr = null;

  $('.lds-facebook').hide();
  $('.ui.dropdown').dropdown();

  var checkLastPage = function() {
    if (page >= lastPage && lastPage != -1) {
      $('.lds-facebook').hide();
      spinStop();
      blocked = true;
      return true;
    }
    return false;
  };

  var spinStart = function() {
    var el = $('.ui.form').find('[type=submit]');
    el
      .addClass('loading')
      .attr('disabled', true);
  };
  var spinStop = function() {
    var el = $('.ui.form').find('[type=submit]');
    el
      .removeClass('loading')
      .attr('disabled', false);
  };
  var fetchData = function(page) {

    xhr = $.ajax({
      method: 'GET',
      url: 'http://13.124.96.124/textsum/' + page + '?query=' + encodeURIComponent(query) + '&category=' + encodeURIComponent(category) + '&hash=' + makeid() + '&access_token=qnswih1vchwyAaz2Uc8rw0CU5rSsOrl6e0BEAqBme3tuI4LfeiHJMFcXmsxiQbXunXifwBLpZbbtzKiyNYwdVA7y53SXWYbKul86qfLjVW0kBSE5miTnuxwj',
    }).done(function(res) {
      lastPage = parseInt(res.last_page);
      res.data.forEach(function(elem) {
        var card = $('<div class="ui fluid card"></div>');
        var content = $('<div class="content"></div>');
        var header = $('<div class="header"></div>');
        var meta = $('<div class="meta"></div>');
        var date = $('<span class="date">');
        var publisher = $('<span class="publisher">');
	var author = $('<span class="author">');
        var description = $('<div class="description"></div>');
        var extraContent = $('<div class="extra content"></div>');
        var readmore = $('<a class="ui button mini compact" target="_blank">더보기</a>');
        var summarized = $('<div class="right floated">요약됨</div>');
        var hashtag = '';
        elem.keywords.forEach(function(keyword, index) {
          if (index !=0) {
            hashtag += ' ';
          }
          hashtag += '#' + keyword;
        });

        extraContent
          .append(readmore);

        if (elem.summarized) {
          extraContent.append(summarized);
        }


        header.text(elem.title);
        description.text(elem.text);
        date.text(elem.created_at);
        if (elem.author) {
          author.text(elem.author + ' 기자');
        }
        publisher.text(elem.publisher);
        meta
          .append(publisher)
          .append('&nbsp;|&nbsp;')
          .append(date)
          .append(author)
          .append('<p class="text-orange">' + hashtag + '</p>');

        readmore.attr('href', elem.url);

        content
          .append(header)
          .append(meta)
          .append(description)

        card
          .append(content)
          .append(extraContent);
        $('#card-content').append(card);

        spinStop();
      });
      if (!checkLastPage()) {
        blocked = false;
        $('.lds-facebook').show();
      }
   });
  }


  function makeid() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 5; i++)
      text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
  }
  checkLastPage();
  $(document).ready(function() {

    $(window).scrollTop(0);
    $('#one-grid').click(function() {
      $('#card-content')
        .removeClass('three')
        .removeClass('one')
        .addClass('one');
    });
    $('.ui.form').submit(function() {
      var el = $(this).find('[type=submit]');
      category = $('[name=category]').val();
      query = $('[name=query]').val();
      if (category == 'news') {
        $('.lds-facebook').show();
        if (xhr) {
          xhr.abort();
        }
        spinStart();
        $('#card-content').html('');
        page = 1;
        fetchData(page);
      } else {
        alert('증권사 보고서와 공시, IR은 준비중입니다.');
      }
      return false;
    });

    $('#three-grid').click(function() {
      $('#card-content')
        .removeClass('one')
        .removeClass('three')
        .addClass('three');
    });

    $(window).scroll(function() {
      if ($(window).scrollTop() >= $(document).height() - $(window).height() - offs && !blocked) {
        blocked = true;
        page += 1;
        fetchData(page);
      }
    });
  });
}(jQuery));

