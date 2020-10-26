/**
 * News functions file.
 */

var News = (function() {

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

        // Mark as read all clicked news
        $(".list-news-a").on("click", function() {
            News.getInstance().setReadNews($(this));
        });

        return {

            /*** Public *******************************************************/

            source: opts.source || null,
            deviceId: opts.deviceId || null,
            deviceType: opts.deviceType || null,
            deviceVersion: opts.deviceVersion || null,

            /**
             * Sets a news a read.
             *
             * @param a object The <a> element to set as read.
             * @return void.
             */
            setReadNews: function(a) {

                if (!$(a).hasClass("readnews")) {
                    var url = "/" + this.source + "/" + this.deviceId + "/" + this.deviceType
                        + "/" + this.deviceVersion + "/ajax/news/setReadNews";
                    $.ajax({
                        url: url,
                        dataType: "json",
                        type: "POST",
                        data: {
                            "data": $(a).attr("data-id")
                        },
                        success: function(res) {

                            $(a).addClass("readnews");
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
