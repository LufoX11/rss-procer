/**
 * Tracking JS functions file.
 */

function Tracking(service, deviceId, deviceType, deviceVersion, appVersion) {

    this.baseUrl = "http://www.rssprocer.com/tracking";
    this.data = {
        service: service || null,
        deviceId: deviceId || null,
        deviceType: deviceType || null,
        deviceVersion: deviceVersion || null,
        appVersion: appVersion || null
    };

    /**
     * Sends new tracking data.
     *
     * @param data array Data to send.
     * @return void.
     */
    this.post = function (data) {

        var url = this.baseUrl + "/post";
        data = $.extend({}, this.data, data);
        $.ajax({
            url: url,
            dataType: "json",
            type: "POST",
            data: {
                "data": data
            }
        });
    };
}
