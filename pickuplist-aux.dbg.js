(function() {
  var datasource, map;
  window.WebFontConfig = {
    google: {
      families: ['Droid+Sans:400,700:latin']
    }
  };
  map = null;
  datasource = '1077209';
  window.init_map = function() {
    return map.showMap('map_canvas', datasource);
  };
  $script(["http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js", "http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js", "pickuplist-map.min.js"], function() {
    return $(document).ready(function() {
      var message, parts, url;
      parts = window.location.href.split(/\//);
      url = "http://pickuplist.com/" + parts[parts.length - 1];
      message = "I just posted a game on @PickupList: " + url;
      $("a[href$='@']").each(function() {
        return $(this).attr("href", "mailto:" + ($(this).attr('href')) + "pickuplist.com");
      });
      $("a[href^='@']").each(function() {
        return $(this).attr("href", "http://twitter.com/" + ($(this).attr('href').substr(1)));
      });
      $("a[href='#facebook']").each(function() {
        return $(this).attr("href", "http://www.facebook.com/dialog/feed?app_id=136699416409291&redirect_uri=" + (encodeURIComponent(url)) + "&message=" + (encodeURIComponent(message.replace('@PickupList', 'PickupList'))) + "&link=" + (encodeURIComponent(url)));
      });
      $("a[href='#tweet']").each(function() {
        return $(this).attr("href", "http://twitter.com/?status=" + (encodeURIComponent(message)));
      });
      $("a[href='#email']").each(function() {
        return $(this).attr("href", "mailto:?subject=" + (encodeURIComponent('I posted on PickupList')) + "&body=" + (encodeURIComponent('Check out my post at ' + url)));
      });
      return map = new pickuplist.Map('init_map');
    });
  });
}).call(this);
