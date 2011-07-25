# google web fonts configuration
window.WebFontConfig =
  google:
    families: [ 'Droid+Sans:400,700:latin' ]

# constant things
datasource = "{{fusion.table}}"
def_newgame = "New Activity"
def_description = "type the description of your activity here; don't forget to include things like schedule and any necessary equipment"
def_email = "email address"

# get a data point by ID
query = (id, callback) ->
  $.getJSON 'getgame.php', { id: id }, (result) ->
    callback result

# get the data for an ID
using_id = (id, callback) ->
  if cached[id]?
    callback cached[id]
  else
    query id, (result) ->
      unless result is null
        cached[id] = result
      callback result

# show an infowindow for a specific id
show_id = (id) -> using_id id, (item) ->
  unless item is null
    content = string_prep displaygame, id, item.data.description, format_date item.data.added
    map.setCenter [ item.latitude, item.longitude ], 14
    map.showInfo content, new google.maps.LatLng item.latitude, item.longitude
    window.location.hash = "#!/#{id}"
  else
    window.location.hash = "#!/"

# get the URL for the google maps directions
directions_url = (id, callback) -> using_id id, (item) ->
  callback "http://maps.google.com/maps?q=to:#{item.latitude},#{item.longitude}"

# go to directions page
goto_directions = (id) ->
  directions_url id, (url) ->
    self.location.href = url

# goto contact page
goto_contact = (id) -> using_id id, (item) ->
  self.location.href = "mailto:#{item.data.contact}"

# flag the post
goto_flag = (id) -> using_id id, (item) ->
  subject = encodeURIComponent "Flag a post (#{id})"
  body = encodeURIComponent "(Let us know why this post is inappropriate or not longer valid and we'll take it down if it's warranted.)\n\n--\nhttp://pickuplist.com/#!/#{id} (#{item.latitude}, #{item.longitude})\n#{item.data.description}\n"
  self.location.href = "mailto:flag@pickuplist.com?subject=#{subject}&body=#{body}"

# replace placeholders in a string
string_prep = (str, replacements...) ->
  newstr = str
  newstr = newstr.replace(new RegExp("\\{\\$" + (i + 1) + "\\}", "g"), replacements[i]) for i in [0..replacements.length]
  newstr

# initialize the maps API (global function)
window.init_map = () ->

  # show the map
  map.showMap 'map_canvas', datasource

  # set up function callbacks
  map.showData_callback (id, data_str, location, callback) ->
    data = eval "(#{data_str})"
    window.location.hash = "#!/#{id}"
    string_prep displaygame, id, data.description, format_date data.added
  map.newData_callback (location) ->
    string_prep newgame, def_newgame, def_description, def_email, location.lat(), location.lng()
  map.hideData_callback () ->
    window.location.hash = "#!/"

  # process the hash url
  parts = window.location.hash.split /\//
  if parts.length > 1 and parts[1].length isnt 0
    id = parts[1]
    action = (if parts.length >= 3 then parts[2...parts.length].join('/') else null)
    if action is 'directions'
      goto_directions id
    else if action is 'contact'
      goto_contact id
    else if action is 'flag'
      goto_flag id
    else
      show_id id
  else
    window.location.hash = "#!/"
    if Modernizr.geolocation
      navigator.geolocation.getCurrentPosition (position) ->
        map.setCenter position, 10

# format the date string how we like it: Monday, June 27, 2011
format_date = (date_str) ->
  d = new Date date_str
  weekdays = [ "Sunday", "Monday", "Tuesday",
               "Wednesday", "Thursday",
               "Friday", "Saturday" ]
  months = [ "January", "February", "March",
             "April", "May", "June", "July",
             "August", "September", "October",
             "November", "December" ]
  weekdays[d.getDay()] + ", " +
    months[d.getMonth()] + " " +
    d.getDate() + ", " + d.getFullYear()

# window resize event handler
window_resize = () ->
  w = $(@).width()
  $("#keywords").css "width", (if w < 860 then 180 else w - 680) + "px"

  h = $(@).height()
  h_map = (if h - 113 < 460 then 460 else h - 113)
  $("#map").css "height", "#{h_map}px"

# location field change event handler
location_change = () ->
  map.setLocation $("#location").val()

# keywords change event handler
keywords_change = () ->
  map.filter $("#keywords").val()

# elementary email validation
email_validate = (email) ->
  at_pos = email.indexOf "@"
  dot_pos = email.lastIndexOf "."
  email isnt def_email and         # must not be the default email text
    dot_pos - at_pos > 1 and       # the @ must be at least 1 char before the .
    at_pos > 0 and                 # the @ can't be the first char
    dot_pos < email.length - 2     # the . must be at least 2 chars before the end

# elementary description validation
description_validate = (descr) ->
  descr isnt def_description and   # must not be the default description text
    descr.split(/\s+/).length > 4  # must be at least 5 words

# get the text of a textarea
textarea_text = (textarea) ->
  text = $(textarea).text()
  val = $(textarea).val()
  (if text.length > val.length then text else val).replace /\n+/g, ''

# save a new game
addgame_save = () ->
  email = $("input.email", $(@).parent())
  email_ok = email_validate email.val()
  email_wasnt_ok = email.hasClass "error"
  descr = $("textarea.description", $(@).parent())
  descr_ok = description_validate textarea_text descr
  descr_wasnt_ok = descr.hasClass "error"
  latitude = $("input.latitude", $(@).parent()).val()
  longitude = $("input.longitude", $(@).parent()).val()

  if email_ok then email.removeClass("error") else email.addClass("error")
  if descr_ok then descr.removeClass("error") else descr.addClass("error")

  errors = []
  if descr_wasnt_ok or email_wasnt_ok
    errors.push "Please type a longer description." unless descr_ok
    errors.push "Please type a valid email address." unless email_ok

  if errors.length > 0
    alert "Error" + (if errors.length > 1 then "s" else "") + ":\n - " + errors.join "\n - "

  if email_ok and descr_ok
    email = email.val()
    descr = textarea_text descr
    $.getJSON 'newgame.php',
      latitude: latitude
      longitude: longitude
      email: email
      description: descr
      (data, status, xhr) =>
        $('div.page1', $(@).parent().parent()).hide()
        $('div.page2', $(@).parent().parent()).show()

# update the links (email, twitter, bookmarks)
update_links = () ->
  # email links
  $("a[href$='@']").each () ->
    $(@).attr "href", "mailto:#{$(@).attr('href')}pickuplist.com"

  # twitter links
  $("a[href^='@']").each () ->
    $(@).attr "href", "http://twitter.com/#{$(@).attr('href').substr(1)}"

# document ready event handler
document_ready = () ->

  # set up event handlers
  $(window).resize(window_resize).resize()
  $("#location").keypress (e) -> location_change() if e.which is 13
  $("#location").blur () -> location_change()
  $("#keywords").keypress (e) -> keywords_change() if e.which is 13
  $("#keywords").blur () -> keywords_change()
  $("button").live "click", addgame_save
  $(".infowindow textarea.default").live "click, keypress, select, focus", () ->
    $(@).removeClass("default").text "";
  $(".infowindow input.default").live "click, keypress, select, focus", () ->
    $(@).removeClass("default").attr "value", "";
  $(".infowindow textarea").live "keypress", (e) ->
    e.preventDefault() if e.which is 13
  $(".infowindow .error").live "keypress", () -> $(@).removeClass "error"

  $(".infowindow a[href$='/directions']").live "click", (e) ->
    e.preventDefault()
    goto_directions $(@).attr("href").split(/\//)[1]

  $(".infowindow a[href$='/contact']").live "click", (e) ->
    e.preventDefault()
    goto_contact $(@).attr("href").split(/\//)[1]

  $(".infowindow a[href$='/flag']").live "click", (e) ->
    e.preventDefault()
    goto_flag $(@).attr("href").split(/\//)[1]

  $("a[href='#closeoverlay']").live "click", (e) ->
    e.preventDefault()
    $("#content_overlay").fadeOut 200

  # create the new game template
  newgame = $(".defaults #add-game").html()
  displaygame = $(".defaults #display-game").html()

  # show the search criteria
  $("#nav").show()

  # update the links (email, twitter, bookmarks)
  update_links()

  # load the map API
  map = new pickuplist.Map 'init_map'

# load scripts
$script [
  "http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"
  "http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"
  "http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.0.6/modernizr.min.js"
  "http://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.3.0/mustache.min.js"
  "pickuplist-map.min.js"
], () -> $(document).ready document_ready