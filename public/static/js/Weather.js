/**
 * Functions file for Weather section.
 */

var Weather = (function () {

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

        $("#weather-location").on("change", function () {

            Weather.getInstance().updateLocation($(this).val());
        });

        return {

            /*** Public *******************************************************/

            source: opts.source || null,
            deviceId: opts.deviceId || null,
            deviceType: opts.deviceType || null,
            deviceVersion: opts.deviceVersion || null,

            updateLocation: function (wlocation) {

                var url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                    + "/" + this.deviceVersion + "/ajax/applet/weather/get/" + wlocation,
                    html = '',
                    i = 0,
                    currentTemperature = '';

                $.mobile.showPageLoadingMsg();
                $.ajax({
                    url: url,
                    dataType: "json",
                    beforeSend: function () {

                        $("#weather-content").fadeTo("slow", 0.5, function () {
                            $.mobile.showPageLoadingMsg();
                        });
                    },
                    complete: function () {

                        $("#weather-content").fadeTo("fast", 1, function () {
                            $.mobile.hidePageLoadingMsg();
                        });
                    },
                    error: function (res) {

                        iMain.showMessage("error", "Ocurrió un error al cambiar la ubicación.");
                        $("#weather-content").fadeTo("fast", 1, function () {
                            $.mobile.hidePageLoadingMsg();
                        });
                    },
                    success: function (res) {

                        // Reload headerWeather applet
                        HeaderWeather.getInstance().getData(true);

                        // Reload weather grid
                        $.each(res.forecast, function (k, v) {
                            if (res.current.temperature) {
                                currentTemperature =
                                    '<p>'
                                        + 'Temp: <strong>' + res.current.temperature + '&deg;</strong> | '
                                        + 'Hum: <strong>' + res.current.humidity + '%</strong>'
                                    + '</p>';
                            }

                            html += ""
                                + '<li data-role="list-divider">' + v.title + '</li>'
                                + '<li class="li-small">'
                                    + '<img src="' + v.icon + '" />'
                                    + '<h3>' + v.description + '</h3>'
                                    + (i === 0 && typeof currentTemperature != "undefined" ?
                                        currentTemperature : '')
                                    + '<p>'
                                        + 'M&aacute;x: <strong>' + v.high + '&deg;</strong> | '
                                        + 'M&iacute;n: <strong>' + v.low + '&deg;</strong>'
                                    + '</p>'
                                + '</li>';
                            i++;
                        });
                        $("#weather-content").hide().empty().html(html).listview("refresh").show();

                        $.mobile.hidePageLoadingMsg();
                    }
                });
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
        getInstance: function (opts) {

            if (_instance === undefined) {
                _instance = init(opts);
            }

            return _instance;
        }
    };
})();
