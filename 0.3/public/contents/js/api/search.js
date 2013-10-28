var Engine_Api_Search = {
    requestedTime : null,
    requestedIdDiv : "",
    requestHandle : null,
    //functions
    page : function(idDiv) {
        this.requestedIdDiv = idDiv;
        this.requestedTime = Date();
        if (this.requestHandle != null) {
            clearTimeout(this.requestHandle);
        }
        this.requestHandle = setTimeout('Engine_Api_Search._call()', 750);
    },
    _call : function() {
        if (this.requestedTime == null) {
            return;
        }
        //dojo.byId(idDiv).innerHTML = text + " - " +what;
        //setto il loader
        dojo.byId(this.requestedIdDiv).innerHTML = "<img src='"+baseUrl+"/contents/images/animations/wait.gif'/>";
        // The URL of the request
        var resultDiv = dojo.byId(this.requestedIdDiv);
        dojo.xhr.post({
            url: "search/ajax/search",
            // No content property -- just send the entire form
            form: dojo.byId("formSearch"),
            // The success handler
            load: function(response) {
                resultDiv.innerHTML = response;
            },
            // The error handler
            error: function() {
                resultDiv.innerHTML = "Error! Retry in few seconds.";
            },
            // The complete handler
            handle: function() {
                hasBeenSent = true;
            }
        });
    }
};