/**
 * Main JS functions file.
 */

var Main = (function() {

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

        if (typeof opts.cache == "undefined") {
            opts.cache = true;
        }

        $.mobile.page.prototype.options.domCache = opts.cache;
        $.mobile.defaultPageTransition = "none";
        $.mobile.transitionFallbacks.slidedown = "none";
        $.mobile.pageLoadErrorMessageTheme = "a";
        $.mobile.selectmenu.prototype.options.nativeMenu = false;

        return {

            /*** Public *******************************************************/

            source: opts.source || null,
            deviceId: opts.deviceId || null,
            deviceType: opts.deviceType || null,
            deviceVersion: opts.deviceVersion || null,
            showMini: opts.showMini || null,
            paths: opts.paths || null,
            appVersion: opts.appVersion || null,
            themes: opts.themes || null,
            themeColor: opts.themeColor || null,
            checkForUpdatesEnabled: false,

            bannerRotationTimer: null,

            /**
             * Displays a box with a custom title and message for the user.
             *
             * @param type string Possible values: "error", "success", "info".
             * @param message string Box message.
             * @return void.
             */
            showMessage: function(type, message) {

                var title = "",
                    html = "";

                switch (type) {
                    case "error":
                        title = 'Error';
                        break;

                    case "success":
                        title = 'Exito';
                        break;

                    case "info":
                        title = 'Información';
                        break;
                }

                html = ''
                    + '<div id="page-messages" data-role="popup" data-dismissible="false" class="ui-corner-all"'
                            + ' data-overlay-theme="a" data-transition="pop">'
                        + '<div data-role="header" class="ui-corner-top">'
                            + "<h1>" + title + "</h1>"
                        + "</div>"
                        + '<div data-role="content" class="ui-corner-bottom ui-content">'
                            + "<h3>" + message + "</h3>"
                            + '<a href="#" data-role="button" data-inline="true" data-rel="back" data-icon="delete">'
                                + "Cerrar"
                            + "</a>"
                        + "</div>"
                    + "</div>";
                $.mobile.activePage.append(html).trigger("pagecreate");
                $("#page-messages").on({ popupafterclose: function () { $(this).remove(); } }).popup("open");
            },

            /**
             * Changes the theme and saves it in user session.
             *
             * @param theme string Theme to use.
             * @return void.
             */
            changeTheme: function(theme) {

                var url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                    + "/" + this.deviceVersion + "/ajax/user/settings/insert/theme";
                $.ajax({
                    url: url,
                    dataType: "json",
                    type: "POST",
                    data: {
                        "data": theme
                    },
                    error: function() {

                        Main.getInstance().showMessage("error", "Ocurrió un error al guardar.");
                    },
                    success: function(res) {

                        Main.getInstance().clearCache();
                        Main.getInstance().updateTheme(res.data);
                        HeaderWeather.getInstance().getData(true);
                        Main.getInstance().showMessage("success", "Tu configuración se guardó.");
                    }
                });
            },

            /**
             * Changes the size of the text and saves in user session.
             *
             * @param increment integet The value to increment (decrement) the current size of the elements.
             * @return void.
             */
            textResize: function(increment) {

                var url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                    + "/" + this.deviceVersion + "/ajax/user/settings/insert/textSize";
                $.ajax({
                    url: url,
                    dataType: "json",
                    type: "POST",
                    data: {
                        "data": increment
                    },
                    error: function() {

                        Main.getInstance().showMessage("error", "Ocurrió un error al guardar.");
                    },
                    success: function(res) {

                        Main.getInstance().updateTextSizes(res.data);
                        Main.getInstance().clearCache();
                        Main.getInstance().showMessage("success", "Tu configuración se guardó.");
                    }
                });
            },

            /**
             * Sends a new message to a friend.
             *
             * @return void.
             */
            recommendApp: function() {

                var url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                        + "/" + this.deviceVersion + "/ajax/contact/recommendApp",
                    name = $("#recommend-name").val(),
                    email = $("#recommend-email").val();

                $.ajax({
                    url: url,
                    dataType: "json",
                    type: "POST",
                    data: {
                        "data": {
                            "name": name,
                            "email": email
                        }
                    },
                    beforeSend: function () {

                        $("#recommend-submit").button("disable");
                        $("#recommend-form").fadeTo("slow", 0.5, function () {
                            $.mobile.showPageLoadingMsg();
                        });
                    },
                    complete: function () {

                        $("#recommend-submit").button("enable");
                        $("#recommend-form").fadeTo("fast", 1, function () {
                            $.mobile.hidePageLoadingMsg();
                        });
                    },
                    error: function (res) {

                        Main.getInstance().showMessage("error", $.parseJSON(res.responseText).data);
                    },
                    success: function (res) {

                        $("#recommend-name, #recommend-email").val("");
                        Main.getInstance().showMessage("success", res.data);
                    }
                });
            },

            /**
             * Sends a new message.
             *
             * @return void.
             */
            saveContact: function() {

                var url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                        + "/" + this.deviceVersion + "/ajax/contact/save",
                    name = $("#about-name").val(),
                    email = $("#about-email").val(),
                    description = $("#about-description").val();

                $.ajax({
                    url: url,
                    dataType: "json",
                    type: "POST",
                    data: {
                        "data": {
                            "name": name,
                            "email": email,
                            "description": description
                        }
                    },
                    beforeSend: function () {

                        $("#about-submit").button("disable");
                        $("#about-form").fadeTo("slow", 0.5, function () {
                            $.mobile.showPageLoadingMsg();
                        });
                    },
                    complete: function () {

                        $("#about-submit").button("enable");
                        $("#about-form").fadeTo("fast", 1, function () {
                            $.mobile.hidePageLoadingMsg();
                        });
                    },
                    error: function (res) {

                        Main.getInstance().showMessage("error", $.parseJSON(res.responseText).data);
                    },
                    success: function (res) {

                        $("#about-name, #about-email, #about-description").val("");
                        Main.getInstance().showMessage("success", res.data);
                    }
                });
            },

            /**
             * Updates the application (deletes cache and restarts the platform.
             *
             * @return void.
             */
            appUpdate: function() {

                // Reloads the page ignoring cache
                window.location.reload(true);
            },

            /**
             * Updates the current application fonts.
             *
             * @param textSize integer The value to increment/decrement.
             * @return void.
             */
            updateTextSizes: function(textSize) {

                var url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                        + "/" + this.deviceVersion + "/ajax/application/getCurrentTextSizes";
                $.getJSON(url, { "size": textSize }, function(res) {

                    $(".css-customization").html('<style type="text/css">' + res.data + '</style>');
                });
            },

            /**
             * Updates the current application theme.
             *
             * @param theme string The theme to update to.
             * @return void.
             */
            updateTheme: function(theme) {

                var themeColor = 'white';

                // Reload page CSSs
                $(".link-theme-file").attr("href", this.paths.css + "/themes/" + theme + ".min.css?"
                    + this.appVersion);
                $.each(this.themes, function (k, v) {
                    if ($.inArray(theme, v) >= 0) {
                        themeColor = k;
                        return;
                    }
                });

                // For custom styling in common-black.css / common-white.css
                $.each(this.themes, function (k, v) {
                    $(".ui-page").removeClass(v.join(" "));
                });
                $(".ui-page").addClass(theme);

                this.themeColor = themeColor;
                $(".link-theme-common-file").attr("href", this.paths.css + "/themes/common-"
                    + themeColor + ".css?" + this.appVersion);

                // Reload config page icons
                $("#config-textsize-icon").attr("src", this.paths.img + "/themes/" + themeColor
                    + "/zoom.png?" + this.appVersion);
                $("#config-theme-icon").attr("src", this.paths.img + "/themes/" + themeColor
                    + "/palette.png?" + this.appVersion);
                $("#config-info-icon").attr("src", this.paths.img + "/themes/" + themeColor
                    + "/info.png?" + this.appVersion);
                $.each($("#panel-menu-main .ui-link-inherit .ui-li-icon.ui-li-thumb"), function (k, v) {
                    $(this).attr("src", Main.getInstance().paths.img + "/themes/" + themeColor
                        + "/menu-" + $(this).attr("data-icon-name") + ".png?" + Main.getInstance().appVersion);
                });
            },

            /**
             * Checks for application updates.
             *
             * @param time integer Time to wait (in milliseconds) to check again.
             * @return void.
             */
            checkForUpdates: function(time) {

                var url = "",
                    _this = this;

                // This flag is to avoid the checking as the first action on the application load
                // and prevents continuous app reloads if something goes wrong
                if (this.checkForUpdatesEnabled) {
                    url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                        + "/" + this.deviceVersion + "/ajax/application/getAppVersion";
                    $.ajax({
                        url: url,
                        dataType: "json",
                        type: "GET",
                        success: function(res) {

                            if (typeof res.appVersion != "undefined"
                                && typeof Main.getInstance().appVersion != "undefined"
                                && res.appVersion != Main.getInstance().appVersion)
                            {
                                Main.getInstance().appUpdate();
                            }
                        }
                    });
                } else {
                    this.checkForUpdatesEnabled = true;
                }

                time = time || 600000; // 1000 * 60 * 10 (10 mins)
                setTimeout(function() { _this.checkForUpdates(time); }, time);
            },

            /**
             * Timer for cleaning the pages cache.
             *
             * @param time integer Time to wait (in milliseconds) to drop the cache.
             * @return void.
             */
            clearCacheTimer: function(time) {

                this.clearCache();

                var _this = this;
                time = time || 1200000; // 1000 * 60 * 20 (20 min).
                setTimeout(function() { _this.clearCacheTimer(time); }, time);
            },

            /**
             * Switches among banners each X time.
             *
             * @param bannersContainer string DIV class of the banners container.
             * @param time integer Time in milliseconds for the switch.
             * @return void.
             */
            startBannerRotation: function(bannersContainer, time) {

                var cBanner = '',
                    nBanner = '',
                    _this = this;

                if (!(banners = $("." + bannersContainer)) || banners.length <= 1) {
                    return;
                }

                // This condition is to avoid rotating immediatly after first page load
                if (this.bannerRotationTimer) {
                    $.each(banners, function(i, v) {

                        // We get the current active element
                        if ($(v).css('display') != 'none') {
                            cBanner = v;

                        // We assume the next element to the current element in the stack as the new one
                        } else if (cBanner) {
                            nBanner = v;
                            return;
                        }
                    });

                    // We get this situation when the current element is the last in the stack
                    if (!nBanner) {
                        nBanner = banners[0];
                    }

                    $(cBanner).fadeOut('fast', function() {
                        $(nBanner).fadeIn('fast');
                        $(this).hide();
                    });
                }

                time = time || 20000; // 1000 * 20 (20 secs).
                clearTimeout(_this.bannerRotationTimer);
                this.bannerRotationTimer =
                    setTimeout(function() { _this.startBannerRotation(bannersContainer, time); }, time);
            },

            /**
             * Deletes all cached pages.
             *
             * @return void.
             */
            clearCache: function() {

                $('div[data-role="page"]:not(".ui-page-active")').remove();
            },

            /**
             * Loads in DOM all the requested pages.
             *
             * @param pages array Pages sources to load (relatives to base URL).
             * @return void.
             */
            preloadPages: function(pages) {

                url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                    + "/" + this.deviceVersion + "/";

                $.each(pages, function (k, v) {
                    $("#page-home, #page-topic").delay(500).queue(function() {
                        $.mobile.loadPage(url + v, { prefetch: "true" });
                        $(this).dequeue();
                    });
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
        getInstance: function(opts) {

            if (_instance === undefined) {
                _instance = init(opts);
            }

            return _instance;
        }
    };
})();
