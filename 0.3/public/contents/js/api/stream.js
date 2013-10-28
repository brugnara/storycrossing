var Engine_Api_Stream = {
    requestedIdDiv : "",
    requestHandle : null,
    lastId : 0,
    getStream : function(idDiv) {
        this.requestedIdDiv = idDiv;
        if (this.requestHandle != null) {
            clearTimeout(this.requestHandle);
        }
        this._call(this.lastId);
    },
    _call : function(lastId) {
        //setto il loader
//        dojo.byId(this.requestedIdDiv).innerHTML = "<img src='"+baseUrl+"/contents/images/animations/wait.gif'/>"+dojo.byId(this.requestedIdDiv).innerHTML;
        // The URL of the request
        var resultDiv = dojo.byId(this.requestedIdDiv);
        var currObj = this;
        dojo.xhr.post({
            url: baseUrl+"/stream/ajax/get/last/"+lastId,
            // No content property -- just send the entire form
            form: dojo.byId("formSearch"),
            // The success handler
            load: function(response) {
                var match = response.match(/MAXID:([0-9]+):/);
                if (match) {
                    if (match[1] != 0) {
                        currObj.lastId = match[1];
                        resultDiv.innerHTML = response+resultDiv.innerHTML;
                    }
                }
                currObj.requestHandle = setTimeout('Engine_Api_Stream._call('+currObj.lastId+')', 15000);
                currObj.reloadDates();
                currObj.checkStream();
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
    },
    reloadDates : function() {
        //scorro tutti i td con id= date_TIMESTAMP
        try {
            var tds = document.getElementsByName("tdDate");
            var tmp = '';
            for (var i=0;i<tds.length;i++) {
                var match = tds[i].id.match(/date_([0-9]+)/);
                if (match) {
                    dojo.byId(tds[i].id).innerHTML = this.advancedDateFormat(match[1]);
                }
            }
        } catch(e) {
            //alert(e.message);
        }
    },
    checkStream : function() {
        try {
            var divs = document.getElementsByName("divStream");
            var counter = 0,divToRemove = [];
            for (var i=0;i<divs.length;i++) {
                counter++;
                if (counter > 10) {
                    divToRemove.push(divs[i]);
                }
            }
        } catch(e) {
//            alert(e.message);
        }
        for (i=0;i<divToRemove.length;i++) {
            divToRemove[i].parentNode.removeChild(divToRemove[i]);
        }
    },
    advancedDateFormat : function(data) {
        var dateStrings = {
            "0:19" : "Seconds ago",
            "20:59" : "Less than a minute ago",
            "60:599" : "A few minutes ago",
            "600:1199" : "Ten minutes ago",
            "1200:1799" : "Twenty minutes ago",
            "1800:3599" : "A half hour ago",
            "3600:7199" : "A hour ago",
            "7200:35999" : "Today",
            "36000:86399" : "Yesterday",
            "86400:604799" : "Days ago", //day (Lunedì/Martedì..)
            "604800:1209599" : "One week ago",
            "1209600:2419199" : "Two weeks ago",
            "2419200:3628799" : "Three weeks ago",
            "3628800:4838399" : "A month ago",
            "4838400:58060799" : "%dd/MM",
            "58060800:-1" : "%dd/MM/yyyy"
        };
//        $tsData = date_timestamp_get(date_create($data));
//        $tsNow = date_timestamp_get(date_create());
//        var tsData = Date(data),
        var tsData = (data),
        tsNow = Date.now()/1000,
        diff = tsNow - tsData,
        dk,ds,tmp,dataStr,min,max;
        if (diff < 0) {
            diff+= 3600;//workaround per fuso orario
        }
//        alert(tsNow+"-"+tsData+"="+diff);
        for (dk in dateStrings) {
            ds = dateStrings[dk];
            tmp = dk.split(":");
            min = tmp[0];
            max = tmp[1];
            if (max == -1 || (min <= diff && diff <= max)) {
                tmp = ds;
                if (tmp.match(/%/)) {
                    tmp = tmp.substr(1);
                    tmp = tsData.toString(tmp);
                }
                dataStr = tmp;
                break;
            }
        }
        return dataStr;
    }
};