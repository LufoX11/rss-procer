/**
 * Header-Weather applet functions file.
 */

var HeaderWeather = (function() {

    /*** Private **************************************************************/

    /**
     * @var object Object instance.
     */
    var _instance;

    /**
     * Initialization.
     *
     * @param opts array Initialization options.
     * @return object An object instance.
     */
    function init(opts) {

        return {

            /*** Public *******************************************************/

            source: opts.source || null,
            deviceId: opts.deviceId || null,
            deviceType: opts.deviceType || null,
            deviceVersion: opts.deviceVersion || null,

            /**
             * @var integer Refresh time in miliseconds. Default is 1 min (60000).
             */
            refreshTime: 60000,

            /**
             * @var object Reference to getData timer.
             */
            timer: null,

            /**
             * Starts polling data.
             *
             * @return void.
             */
            init: function() {

                if (this.timer) {
                    this.getData();
                } else {
                    this.timer = window.setInterval(
                        function () { HeaderWeather.getInstance().getData(true); }, this.refreshTime);
                }
            },

            /**
             * Gets weather data from user location.
             *
             * @param force boolean If we should force to retrieve data from API instead of cache.
             * @return void.
             */
            getData: function(force) {

                var currentPage = "#" + $.mobile.activePage.attr("id");
                if (typeof force == "undefined") {
                    force = false;
                }

                // From local cache
                if (!force && (currentWeather = iStorage.get("currentWeather")).isTrue()) {
                    $(currentPage + " .applet-header-weather").html(currentWeather);
                    $(currentPage + " .applet-header-weather > ul").listview();
                } else {
                    var url = "/" + HeaderWeather.getInstance().source + "/"
                        + HeaderWeather.getInstance().deviceId + "/"
                        + HeaderWeather.getInstance().deviceType + "/"
                        + HeaderWeather.getInstance().deviceVersion + "/ajax/applet/headerWeather/get";

                    $.ajax({
                        url: url,
                        dataType: "json",
                        error: function(res) {

                            if (res.responseText) {
                                res = $.parseJSON(res.responseText);
                            }
                            if (res.icon) {
                                var currentWeather = ""
                                    + '<ul data-role="listview" data-corners="false" data-inset="true">'
                                        + '<li data-role="list-divider">Clima</li>'
                                        + '<li>'
                                            + '<img src="' + res.icon + '" />'
                                            + '<h3>No disponible.</h3>'
                                            + '<p>Revisá después.</p>'
                                        + '</li>'
                                    + '</ul>';
                                iStorage.set("currentWeather", currentWeather);
                                $(currentPage + " .applet-header-weather").html(currentWeather);
                                $(currentPage + " .applet-header-weather > ul").listview();
                            }
                        },
                        success: function(res) {

                            if (res.temperature) {
                                var currentWeather = ""
                                    + '<ul data-role="listview" data-corners="false" data-inset="true">'
                                        + '<li data-role="list-divider">Clima</li>'
                                        + '<li>'
                                            + '<img class="weather-icon" src="' + res.icon + '" />'
                                            + '<h3 class="location">' + res.location + '</h3>'
                                            + '<p class="date-weather">' + res.date + '</p>'
                                            + '<p class="temp-weather">'
                                                + '<strong>' + res.temperature + '</strong>&deg; '
                                                + res.humidity + '%'
                                            + '</p>'
                                        + '</li>'
                                    + '</ul>';

                                iStorage.set("currentWeather", currentWeather);
                                $(currentPage + " .applet-header-weather").html(currentWeather);
                                $(currentPage + " .applet-header-weather > ul").listview();
                            }
                        }
                    });
                }
            }
        }
    }

    return {

        /**
         * (Creates and) Gets the current instance.
         *
         * @param opts array Initialization options.
         * @return object An object instance.
         */
        getInstance: function(opts) {

            if (_instance === undefined) {
                _instance = init(opts);
            }

            return _instance;
        }
    };
})();
