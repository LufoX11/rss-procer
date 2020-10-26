/**
 * Useful functions.
 */
var Utils = {

    init: function () {

        ////
        // Some JS improvement functions.
        ////

        // Tells whether the value is TRUE in a way that "0" of "false" would be FALSE
        String.prototype.isTrue = function() {

            return Boolean((isNaN(this) && this.toLowerCase() !== "false" && this.toLowerCase() !== "null")
                || parseInt(this));
        }
    }
};
