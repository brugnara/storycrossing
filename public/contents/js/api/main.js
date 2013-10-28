var Engine_Api_Main = {
    requestHandle : null,
    getTags : function(idDiv) {
        this._call('ajax/tags',idDiv);
    },
    getTopWriters : function(idDiv) {
        this._call('ajax/topwriters',idDiv);
    },
    getTopWritersEver : function(idDiv) {
        this._call('ajax/topwriters/showall/1',idDiv);
    },
    getShowCase : function(idDiv) {
        this._call('ajax/showcase',idDiv);
    },
    mostFollowed : function(idDiv) {
        this._call('ajax/mostfollowed',idDiv);
    },
    getLastActiveUsers : function(idDiv) {
        this._call('ajax/lastactiveusers',idDiv);
    },
    _call : function(service,destinationDiv) {
        //setto il loader
//        dojo.byId(this.requestedIdDiv).innerHTML = "<img src='"+baseUrl+"/contents/images/animations/wait.gif'/>"+dojo.byId(this.requestedIdDiv).innerHTML;
        // The URL of the request
        var resultDiv = dojo.byId(destinationDiv);
        dojo.xhr.post({
            url: baseUrl+"/"+service,
            // The success handler
            load: function(response) {
                resultDiv.innerHTML = response;
            },
            // The error handler
            error: function() {
                resultDiv.innerHTML = "Error! Reload this page.";
            },
            // The complete handler
            handle: function() {
                hasBeenSent = true;
            }
        });
    }
};