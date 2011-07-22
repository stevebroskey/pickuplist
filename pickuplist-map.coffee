window.pickuplist ?= {}

class pickuplist.Map

  constructor: (callback_name) ->
    script = document.createElement 'script'
    script.type = 'text/javascript'
    script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=#{callback_name}"
    document.body.appendChild script

  showMap: (element_id, @datasource, center, zoom) ->
    @geocoder = new google.maps.Geocoder()
    @map = new google.maps.Map document.getElementById(element_id),
      zoom: zoom ? 4
      center: center ? new google.maps.LatLng(37.997, -95.801)
      mapTypeId: google.maps.MapTypeId.ROADMAP
      backgroundColor: '#99b3cc'

    @fusion = new google.maps.FusionTablesLayer
      suppressInfoWindows: true
      map: @map
      query:
        select: 'latitude'
        from: @datasource
      styles: [
        markerOptions:
          iconName: 'large_red'
      ]

    google.maps.event.addListener @fusion, 'click', (event) =>
      if @showData?
        location = new google.maps.LatLng event.row.latitude.value, event.row.longitude.value
        @showInfo @showData(event.row.id.value, event.row.data.value, location),
          location, event.pixelOffset

    google.maps.event.addListener @map, 'rightclick', (event) =>
      if @newData?
        @showInfo @newData(event.latLng), event.latLng, new google.maps.Size 0, 0

  showData_callback: (@showData) ->
  newData_callback: (@newData) ->
  hideData_callback: (@hideData) ->
  idle_callback: (@idleAction) ->

  showInfo: (content, position, offset) ->
    offset ?= new google.maps.Size 0, -32, 'px', 'px'
    @infowindows ?= []
    w.close() for w in @infowindows
    @infowindows.length = 0
    iw = new google.maps.InfoWindow
      content: content
      position: position
      pixelOffset: offset
    google.maps.event.addListener iw, "closeclick", () =>
      @hideData?()
    iw.open @map
    @infowindows.push iw

  setCenter: (center, zoom) ->
    if typeof center is 'object'
      if center.coords?
        @map.setCenter new google.maps.LatLng center.coords.latitude, center.coords.longitude
      else
        @map.setCenter new google.maps.LatLng center[0], center[1]
    else
      @map.setCenter center

    if zoom?
      @setZoom zoom

  setZoom: (zoom) ->
    @map.setZoom zoom

  setLocation: (location) ->
    if typeof location is 'object'
      @setCenter location
    else unless location.trim() is ""
      @geocode location, (results) => @map.fitBounds results[0].geometry.viewport

  filter: (keywords) ->
    words = (k.replace("'", "\\'") for k in keywords.trim().split /\s+/)
    options =
      query:
        select: 'latitude'
        from: @datasource
        where: null
      styles: [
        markerOptions:
          iconName: 'large_red'
      ]
    if words[0].length isnt 0
      options.query.where = ("data CONTAINS IGNORING CASE '#{w}'" for w in words).join ' AND '
      options.styles[0].markerOptions.iconName = 'large_yellow'
    @fusion.setOptions options

  geocode: (params...) ->
    geocode_options = { }
    if params.length is 2
      if typeof params[0] is 'string'
        geocode_options =
          address: params[0]
      else if typeof params[0] is 'object'
        geocode_options =
          latLng: params[0]
    else if params.length is 3
      geocode_options =
        latLng: new google.maps.LatLng params[0], params[1]
    @geocoder.geocode geocode_options, (results, status) ->
      if status is google.maps.GeocoderStatus.OK
        params[params.length - 1] results