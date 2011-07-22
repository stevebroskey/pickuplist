(function() {
  var _ref;
  var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; }, __slice = Array.prototype.slice;
    if ((_ref = window.pickuplist) != null) {
    _ref;
  } else {
    window.pickuplist = {};
  };
  pickuplist.Map = (function() {
    function Map(callback_name) {
      var script;
      script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=" + callback_name;
      document.body.appendChild(script);
    }
    Map.prototype.showMap = function(element_id, datasource, center, zoom) {
      this.datasource = datasource;
      this.geocoder = new google.maps.Geocoder();
      this.map = new google.maps.Map(document.getElementById(element_id), {
        zoom: zoom != null ? zoom : 4,
        center: center != null ? center : new google.maps.LatLng(37.997, -95.801),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        backgroundColor: '#99b3cc'
      });
      this.fusion = new google.maps.FusionTablesLayer({
        suppressInfoWindows: true,
        map: this.map,
        query: {
          select: 'latitude',
          from: this.datasource
        },
        styles: [
          {
            markerOptions: {
              iconName: 'large_red'
            }
          }
        ]
      });
      google.maps.event.addListener(this.fusion, 'click', __bind(function(event) {
        var location;
        if (this.showData != null) {
          location = new google.maps.LatLng(event.row.latitude.value, event.row.longitude.value);
          return this.showInfo(this.showData(event.row.id.value, event.row.data.value, location), location, event.pixelOffset);
        }
      }, this));
      return google.maps.event.addListener(this.map, 'rightclick', __bind(function(event) {
        if (this.newData != null) {
          return this.showInfo(this.newData(event.latLng), event.latLng, new google.maps.Size(0, 0));
        }
      }, this));
    };
    Map.prototype.showData_callback = function(showData) {
      this.showData = showData;
    };
    Map.prototype.newData_callback = function(newData) {
      this.newData = newData;
    };
    Map.prototype.hideData_callback = function(hideData) {
      this.hideData = hideData;
    };
    Map.prototype.idle_callback = function(idleAction) {
      this.idleAction = idleAction;
    };
    Map.prototype.showInfo = function(content, position, offset) {
      var iw, w, _i, _len, _ref2, _ref3;
            if (offset != null) {
        offset;
      } else {
        offset = new google.maps.Size(0, -32, 'px', 'px');
      };
            if ((_ref2 = this.infowindows) != null) {
        _ref2;
      } else {
        this.infowindows = [];
      };
      _ref3 = this.infowindows;
      for (_i = 0, _len = _ref3.length; _i < _len; _i++) {
        w = _ref3[_i];
        w.close();
      }
      this.infowindows.length = 0;
      iw = new google.maps.InfoWindow({
        content: content,
        position: position,
        pixelOffset: offset
      });
      google.maps.event.addListener(iw, "closeclick", __bind(function() {
        return typeof this.hideData === "function" ? this.hideData() : void 0;
      }, this));
      iw.open(this.map);
      return this.infowindows.push(iw);
    };
    Map.prototype.setCenter = function(center, zoom) {
      if (typeof center === 'object') {
        if (center.coords != null) {
          this.map.setCenter(new google.maps.LatLng(center.coords.latitude, center.coords.longitude));
        } else {
          this.map.setCenter(new google.maps.LatLng(center[0], center[1]));
        }
      } else {
        this.map.setCenter(center);
      }
      if (zoom != null) {
        return this.setZoom(zoom);
      }
    };
    Map.prototype.setZoom = function(zoom) {
      return this.map.setZoom(zoom);
    };
    Map.prototype.setLocation = function(location) {
      if (typeof location === 'object') {
        return this.setCenter(location);
      } else if (location.trim() !== "") {
        return this.geocode(location, __bind(function(results) {
          return this.map.fitBounds(results[0].geometry.viewport);
        }, this));
      }
    };
    Map.prototype.filter = function(keywords) {
      var k, options, w, words;
      words = (function() {
        var _i, _len, _ref2, _results;
        _ref2 = keywords.trim().split(/\s+/);
        _results = [];
        for (_i = 0, _len = _ref2.length; _i < _len; _i++) {
          k = _ref2[_i];
          _results.push(k.replace("'", "\\'"));
        }
        return _results;
      })();
      options = {
        query: {
          select: 'latitude',
          from: this.datasource,
          where: null
        },
        styles: [
          {
            markerOptions: {
              iconName: 'large_red'
            }
          }
        ]
      };
      if (words[0].length !== 0) {
        options.query.where = ((function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = words.length; _i < _len; _i++) {
            w = words[_i];
            _results.push("data CONTAINS IGNORING CASE '" + w + "'");
          }
          return _results;
        })()).join(' AND ');
        options.styles[0].markerOptions.iconName = 'large_yellow';
      }
      return this.fusion.setOptions(options);
    };
    Map.prototype.geocode = function() {
      var geocode_options, params;
      params = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
      geocode_options = {};
      if (params.length === 2) {
        if (typeof params[0] === 'string') {
          geocode_options = {
            address: params[0]
          };
        } else if (typeof params[0] === 'object') {
          geocode_options = {
            latLng: params[0]
          };
        }
      } else if (params.length === 3) {
        geocode_options = {
          latLng: new google.maps.LatLng(params[0], params[1])
        };
      }
      return this.geocoder.geocode(geocode_options, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
          return params[params.length - 1](results);
        }
      });
    };
    return Map;
  })();
}).call(this);
