/**
 * Local storage layer.
 */

var Storage = (function () {

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
             * Saves a persisten value.
             *
             * @param name string Key.
             * @param value mixed Value to save (will be casted to string).
             * @return void.
             */
            set: function (name, value) {

                localStorage.setItem(name, value);
            },

            /**
             * Retrieves a value from the storage.
             *
             * @param name string Key.
             * @return mixed String with the value if the name is found; NULL otherwise.
             */
            get: function (name) {

                return String(localStorage.getItem(name));
            },

            /**
             * Removes a value in the storage.
             *
             * @param name string Key.
             * @return void.
             */
            unset: function (name) {

                localStorage.removeItem(name);
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
