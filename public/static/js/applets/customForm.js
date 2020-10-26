/**
 * Custom form applet functions file.
 */

var CustomForm = (function() {

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

            /**
             * Sends a new support ticket.
             *
             * @return void.
             */
            sendSupportContact: function() {

                var url = "/" + Main.getInstance().source + "/"
                    + Main.getInstance().deviceId + "/"
                    + Main.getInstance().deviceType + "/"
                    + Main.getInstance().deviceVersion + "/ajax/contact/sendSupportTicket",
                    data = { "data": {} },
                    dataElements = $(".customForm-data");

                $.each(dataElements, function (k, v) {
                    k = $(v).attr("data-title") + "|" + $(v).attr("data-type") + "|" + $(v).attr("data-required");
                    v = $(v).val();
                    data["data"][k] = v;
                });

                $.ajax({
                    url: url,
                    dataType: "json",
                    type: "POST",
                    data: data,
                    beforeSend: function () {

                        $("#customForm-submit").button("disable");
                        $("#customForm-form").fadeTo("slow", 0.5, function () {
                            $.mobile.showPageLoadingMsg();
                        });
                    },
                    complete: function () {

                        $("#customForm-submit").button("enable");
                        $("#customForm-form").fadeTo("fast", 1, function () {
                            $.mobile.hidePageLoadingMsg();
                        });
                    },
                    error: function (res) {

                        Main.getInstance().showMessage("error", $.parseJSON(res.responseText).data);
                    },
                    success: function (res) {

                        $(".customForm-data").val("");
                        Main.getInstance().showMessage("success", res.data);
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
        getInstance: function(opts) {

            if (_instance === undefined) {
                _instance = init(opts);
            }

            return _instance;
        }
    };
})();
