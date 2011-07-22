(function() {
  var addgame_save, cached, datasource, def_description, def_email, def_newgame, description_validate, directions_url, displaygame, document_ready, email_validate, format_date, goto_contact, goto_directions, goto_flag, keywords_change, location_change, map, newgame, query, show_id, string_prep, textarea_text, update_links, using_id, window_resize;
  var __slice = Array.prototype.slice, __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };
  window.WebFontConfig = {
    google: {
      families: ['Droid+Sans:400,700:latin']
    }
  };
  map = null;
  newgame = null;
  displaygame = null;
  cached = {};
  datasource = "1077209";
  def_newgame = "New Activity";
  def_description = "type the description of your activity here; don't forget to include things like schedule and any necessary equipment";
  def_email = "email address";
  query = function(id, callback) {
    return $.getJSON('getgame.php', {
      id: id
    }, function(result) {
      return callback(result);
    });
  };
  using_id = function(id, callback) {
    if (cached[id] != null) {
      return callback(cached[id]);
    } else {
      return query(id, function(result) {
        if (result !== null) {
          cached[id] = result;
        }
        return callback(result);
      });
    }
  };
  show_id = function(id) {
    return using_id(id, function(item) {
      var content;
      if (item !== null) {
        content = string_prep(displaygame, id, item.data.description, format_date(item.data.added));
        map.setCenter([item.latitude, item.longitude], 14);
        map.showInfo(content, new google.maps.LatLng(item.latitude, item.longitude));
        return window.location.hash = "#!/" + id;
      } else {
        return window.location.hash = "#!/";
      }
    });
  };
  directions_url = function(id, callback) {
    return using_id(id, function(item) {
      return callback("http://maps.google.com/maps?q=to:" + item.latitude + "," + item.longitude);
    });
  };
  goto_directions = function(id) {
    return directions_url(id, function(url) {
      return self.location.href = url;
    });
  };
  goto_contact = function(id) {
    return using_id(id, function(item) {
      return self.location.href = "mailto:" + item.data.contact;
    });
  };
  goto_flag = function(id) {
    return using_id(id, function(item) {
      var body, subject;
      subject = encodeURIComponent("Flag a post (" + id + ")");
      body = encodeURIComponent("(Let us know why this post is inappropriate or not longer valid and we'll take it down if it's warranted.)\n\n--\nhttp://pickuplist.com/#!/" + id + " (" + item.latitude + ", " + item.longitude + ")\n" + item.data.description + "\n");
      return self.location.href = "mailto:flag@pickuplist.com?subject=" + subject + "&body=" + body;
    });
  };
  string_prep = function() {
    var i, newstr, replacements, str, _ref;
    str = arguments[0], replacements = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
    newstr = str;
    for (i = 0, _ref = replacements.length; 0 <= _ref ? i <= _ref : i >= _ref; 0 <= _ref ? i++ : i--) {
      newstr = newstr.replace(new RegExp("\\{\\$" + (i + 1) + "\\}", "g"), replacements[i]);
    }
    return newstr;
  };
  window.init_map = function() {
    var action, id, parts;
    map.showMap('map_canvas', datasource);
    map.showData_callback(function(id, data_str, location, callback) {
      var data;
      data = eval("(" + data_str + ")");
      window.location.hash = "#!/" + id;
      return string_prep(displaygame, id, data.description, format_date(data.added));
    });
    map.newData_callback(function(location) {
      return string_prep(newgame, def_newgame, def_description, def_email, location.lat(), location.lng());
    });
    map.hideData_callback(function() {
      return window.location.hash = "#!/";
    });
    parts = window.location.hash.split(/\//);
    if (parts.length > 1 && parts[1].length !== 0) {
      id = parts[1];
      action = (parts.length >= 3 ? parts.slice(2, parts.length).join('/') : null);
      if (action === 'directions') {
        return goto_directions(id);
      } else if (action === 'contact') {
        return goto_contact(id);
      } else if (action === 'flag') {
        return goto_flag(id);
      } else {
        return show_id(id);
      }
    } else {
      window.location.hash = "#!/";
      if (Modernizr.geolocation) {
        return navigator.geolocation.getCurrentPosition(function(position) {
          return map.setCenter(position, 10);
        });
      }
    }
  };
  format_date = function(date_str) {
    var d, months, weekdays;
    d = new Date(date_str);
    weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    return weekdays[d.getDay()] + ", " + months[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear();
  };
  window_resize = function() {
    var h, h_map, h_message, w;
    w = $(this).width();
    $("#keywords").css("width", (w < 860 ? 180 : w - 680) + "px");
    h = $(this).height();
    h_map = (h - 160 < 460 ? 460 : h - 160);
    $("#map .middle").css("height", "" + h_map + "px");
    h_message = $('#content_overlay .message').innerHeight();
    return $("#content_overlay .message").css('top', ((h_map - h_message) / 2) + 'px');
  };
  location_change = function() {
    return map.setLocation($("#location").val());
  };
  keywords_change = function() {
    return map.filter($("#keywords").val());
  };
  email_validate = function(email) {
    var at_pos, dot_pos;
    at_pos = email.indexOf("@");
    dot_pos = email.lastIndexOf(".");
    return email !== def_email && dot_pos - at_pos > 1 && at_pos > 0 && dot_pos < email.length - 2;
  };
  description_validate = function(descr) {
    return descr !== def_description && descr.split(/\s+/).length > 4;
  };
  textarea_text = function(textarea) {
    var text, val;
    text = $(textarea).text();
    val = $(textarea).val();
    return (text.length > val.length ? text : val).replace(/\n+/g, '');
  };
  addgame_save = function() {
    var descr, descr_ok, descr_wasnt_ok, email, email_ok, email_wasnt_ok, errors, latitude, longitude;
    email = $("input.email", $(this).parent());
    email_ok = email_validate(email.val());
    email_wasnt_ok = email.hasClass("error");
    descr = $("textarea.description", $(this).parent());
    descr_ok = description_validate(textarea_text(descr));
    descr_wasnt_ok = descr.hasClass("error");
    latitude = $("input.latitude", $(this).parent()).val();
    longitude = $("input.longitude", $(this).parent()).val();
    if (email_ok) {
      email.removeClass("error");
    } else {
      email.addClass("error");
    }
    if (descr_ok) {
      descr.removeClass("error");
    } else {
      descr.addClass("error");
    }
    errors = [];
    if (descr_wasnt_ok || email_wasnt_ok) {
      if (!descr_ok) {
        errors.push("Please type a longer description.");
      }
      if (!email_ok) {
        errors.push("Please type a valid email address.");
      }
    }
    if (errors.length > 0) {
      alert("Error" + (errors.length > 1 ? "s" : "") + ":\n - " + errors.join("\n - "));
    }
    if (email_ok && descr_ok) {
      email = email.val();
      descr = textarea_text(descr);
      return $.getJSON('newgame.php', {
        latitude: latitude,
        longitude: longitude,
        email: email,
        description: descr
      }, __bind(function(data, status, xhr) {
        $('div.page1', $(this).parent().parent()).hide();
        return $('div.page2', $(this).parent().parent()).show();
      }, this));
    }
  };
  update_links = function() {
    $("a[href$='@']").each(function() {
      return $(this).attr("href", "mailto:" + ($(this).attr('href')) + "pickuplist.com");
    });
    return $("a[href^='@']").each(function() {
      return $(this).attr("href", "http://twitter.com/" + ($(this).attr('href').substr(1)));
    });
  };
  document_ready = function() {
    $(window).resize(window_resize).resize();
    $("#location").keypress(function(e) {
      if (e.which === 13) {
        return location_change();
      }
    });
    $("#location").blur(function() {
      return location_change();
    });
    $("#keywords").keypress(function(e) {
      if (e.which === 13) {
        return keywords_change();
      }
    });
    $("#keywords").blur(function() {
      return keywords_change();
    });
    $("button").live("click", addgame_save);
    $(".infowindow textarea.default").live("click, keypress, select, focus", function() {
      return $(this).removeClass("default").text("");
    });
    $(".infowindow input.default").live("click, keypress, select, focus", function() {
      return $(this).removeClass("default").attr("value", "");
    });
    $(".infowindow textarea").live("keypress", function(e) {
      if (e.which === 13) {
        return e.preventDefault();
      }
    });
    $(".infowindow .error").live("keypress", function() {
      return $(this).removeClass("error");
    });
    $(".infowindow a[href$='/directions']").live("click", function(e) {
      e.preventDefault();
      return goto_directions($(this).attr("href").split(/\//)[1]);
    });
    $(".infowindow a[href$='/contact']").live("click", function(e) {
      e.preventDefault();
      return goto_contact($(this).attr("href").split(/\//)[1]);
    });
    $(".infowindow a[href$='/flag']").live("click", function(e) {
      e.preventDefault();
      return goto_flag($(this).attr("href").split(/\//)[1]);
    });
    $("a[href='#closeoverlay']").live("click", function(e) {
      e.preventDefault();
      return $("#content_overlay").fadeOut(200);
    });
    newgame = $(".defaults #add-game").html();
    displaygame = $(".defaults #display-game").html();
    $("#nav").show();
    update_links();
    return map = new pickuplist.Map('init_map');
  };
  $script(["http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js", "http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js", "http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.0.6/modernizr.min.js", "pickuplist-map.min.js"], function() {
    return $(document).ready(document_ready);
  });
}).call(this);
