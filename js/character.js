$(function(){
    /* Do some javascripts on page load! */
    function addLuckCountdown(target) {
      var span = $("<span class='centered'></span>");
      span.append("<p>Luck</p>");
      var raw_luck = elem.data("luck") != "" ? elem.data("luck") : "0";
      var luck = atoi(raw_luck);
      var boxes = [];
      for ( var i = 0; i < 7; i++ )
      {
        var ticked = ( i < luck ) ? "fa-check-square-o" : "fa-square-o";
        boxes.push("<i class='fa " + ticked + "'></i>");
      }
      var data = boxes.join(" ");
      span.append(data);
      $(target).append(span);
    }

    $("ul.stats").each(function(idx) {
      var stats = $(this).data();
      $.each(stats, function(key, val){
        var op = atoi(val);
        op = op == 0 ? "=" : ((op < 0) ? "-" : "+");
        $(this).append($('<li class="character-sheet stat">' + key + op + val + '</li>'))
      });
    });

    $('.character-delete').each(function(){
      $(this).click(function(e){
        e.preventDefault();
        var slug = $(this).data('slug');
        var id = $(this).data('id');
        if ( id != "" && slug != "" )
        {
          var doDelete = window.confirm("Do you really want to delete " + slug +"?");
          if ( doDelete == true )
          {
            $.post('/discussions/characters/delete/' + id, {}, function(data){
              if ( data.error ) return;
              $("#character-" + id).remove();
            });
          }
        }
      });
    });

    $('.character-dialog').click(function(e){
      e.preventDefault();
      var slug = $('#character-select').val();
      var text = "<div class='dialog' data-char='" + slug + "'>Dialog!</div>";
      $('#Form_Body').val( $('#Form_Body').val() + text );
    }).on('tap', function(e){
      e.preventDefault();
      var slug = $('#character-select').val();
      var text = "<div class='dialog' data-char='" + slug + "'>Dialog!</div>";
      $('#Form_Body').val( $('#Form_Body').val() + text );
    });

    $(function(){
    	$('.dialog').each(function(idx){
        var char = $(this).data('char');
        if ( char )
        {
          var target = this;
          $.get('/api/characters/'  + char, function(data) {
            console.log(data);
            if ( data.hasOwnProperty('CharacterId') )
            {
              var container = $("<div class='character-meta-container'></div>");
              var thumb = $("<a href='" + data.Sheet + "' class='character-thumb'></a>");

              thumb.css("background-image", "url('" + data.ImgThumb + "')");
              $(container).append(thumb);
              $(container).append($("<p><a href='" + data.Sheet + "'>" + data.Name + "</a></p>"));
              $(target).prepend(container);
              $(target).css('min-height', $(container).outerHeight());
              $(target).addClass("processed");
            }
          });
        }
        else
        {
          var container = $("<div class='character-meta-container'></div>");
          var thumb = $("<a href='" + $(this).data("url") + "' class='character-thumb'></a>");
          thumb.css("background-image", "url('" + $(this).data("thumb") + "')");
          $(container).append(thumb);
          $(container).append($("<p><a href='" + $(this).data("url") + "'>" + $(this).data("name") + "</a></p>"));
          $(this).prepend(container);
          $(this).css('min-height', $(container).outerHeight());
          $(this).addClass("processed");
        }
      });
    });

    $('#character-select').change(function(e){
      var character = $(this).val();
      var sheets = JSON.parse($('#character-sheet-link').data('sheets'));
      if ( sheets.hasOwnProperty(slug) )
      {
        $('#character-sheet-link').attr('href', slug).show();
      }
      else {
        $('#character-sheet-link').attr('href', '#').hide();
      }
    })


    $(document).on('CommentAdded', function()
    {
      $('.dialog:not(.processed)').each(function(idx){
        var char = $(this).data('char');
        if ( char )
        {
          var target = this;
          $.get('/api/characters/'  + char, function(data) {
            if ( data.hasOwnProperty('CharacterId') )
            {
              var container = $("<div class='character-meta-container'></div>");
              var thumb = $("<a href='" + data.Sheet + "' class='character-thumb'></a>");

              thumb.css("background-image", "url('" + data.ImgThumb + "')");
              $(container).append(thumb);
              $(container).append($("<p><a href='" + data.Sheet + "'>" + data.Name + "</a></p>"));
              $(target).prepend(container);
              $(target).css('min-height', $(container).outerHeight());
              $(target).addClass("processed");
            }
          });
        }
        else
        {
        	var container = $("<div class='character-meta-container'></div>");
          var thumb = $("<a href='" + $(this).data("url") + "' class='character-thumb'></a>");
          thumb.css("background-image", "url('" + $(this).data("thumb") + "')");
          $(container).append(thumb);
          $(container).append($("<p><a href='" + $(this).data("url") + "'>" + $(this).data("name") + "</a></p>"));
          $(this).prepend(container);
          $(this).css('min-height', $(container).outerHeight());
          $(this).addClass("processed");
        }
      });
    });

	$(".CharacterBanner").each(function(idx){
		$(this).css('background-image', 'url("' + $(this).data('url') + '")');
	});

});
