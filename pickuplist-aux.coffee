# google web fonts configuration
window.WebFontConfig =
  google:
    families: [ 'Droid+Sans:400,700:latin' ]

# stuff to keep track of
map = null             # our map object
datasource = '1077209' # our fusion tables ID

# called when the map API is loaded
window.init_map = () ->
  map.showMap 'map_canvas', datasource

# load dependencies
$script [
  "http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"
  "http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"
  "pickuplist-map.min.js"
], () -> $(document).ready () ->

  # figure out our new post URL
  parts = window.location.href.split /\//
  url = "http://pickuplist.com/#{parts[parts.length - 1]}"
  message = "I just posted a game on @PickupList: #{url}";

  # set up email links
  $("a[href$='@']").each () ->
    $(@).attr "href", "mailto:#{$(@).attr('href')}pickuplist.com"

  # set up twitter links
  $("a[href^='@']").each () ->
    $(@).attr "href", "http://twitter.com/#{$(@).attr('href').substr(1)}"

  # set up facebook sharing links
  $("a[href='#facebook']").each () ->
    $(@).attr "href", "http://www.facebook.com/dialog/feed?app_id=136699416409291&redirect_uri=#{encodeURIComponent url}&message=#{encodeURIComponent message.replace('@PickupList', 'PickupList')}&link=#{encodeURIComponent url}"

  # set up twitter sharing links
  $("a[href='#tweet']").each () ->
    $(@).attr "href", "http://twitter.com/?status=#{encodeURIComponent message}"

  # set up email sharing links
  $("a[href='#email']").each () ->
    $(@).attr "href", "mailto:?subject=#{encodeURIComponent 'I posted on PickupList'}&body=#{encodeURIComponent 'Check out my post at ' + url}"

  # show the map
  map = new pickuplist.Map 'init_map'