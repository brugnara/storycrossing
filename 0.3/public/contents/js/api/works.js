var Engine_Api_Works = {
    _response : null,
    _orderByViews : Array(),
    _orderByVotes : Array(),
    _resultDiv : null,
    getBestBooks : function() {
        dojo.xhr.post({
            url: baseUrl+"users/updates/delupdates/1",
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
    },
    getMostVotedPages : function(idDiv,userid,limit) {
        this._resultDiv = dojo.byId(idDiv);
        var currObj = this;
        this._setLoading();
        dojo.xhr.post({
            url: baseUrl+"/works/getmostvotedpages/limit/"+limit+"/userid/"+userid,
            // The success handler
            load: function(response) {
//                resultDiv.innerHTML = response;
                currObj._response = eval(response);
                //preparo array:
                currObj._prepareArray();
                //
                currObj._resultDiv.innerHTML = currObj._getHtml();
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
    },
    getComments : function() {
        //
    },
    getWall : function() {
        //
    },
    //
    _setLoading : function() {
        this._resultDiv.innerHTML = "<h3>Loading..." + Engine_Api_Images.getBouncingBall() + "</h3>";
    },
    _prepareArray : function() {
        try {
            var j,t,ret = '<ul>';
            for (j=0;j<this._response[0].length;j++) {
                t = this._response[0][j];
                ret+= "<li><b>"+t.title+"</b>, <i>voted "+t.votecount+" times, medium: "+(Math.round(t.vote*10)/10)+"</i></li>";
            }
            ret+= "</ul>";
            this._orderByVotes = ret;
            ret = '';
            for (j=0;j<this._response[1].length;j++) {
                t = this._response[1][j];
                ret+= "<li><b>"+t.title+"</b>, <i>viewed "+t.views+" times</i></li>";
            }
            ret+= "</ul>";
            this._orderByViews = ret;
        } catch(e) {
            //alert(e.message);
        }
    },
    _getHtml : function() {
        var ret =
        "<div>"+
            "<a href='#' onclick=\"Engine_Api_Utility.flip('mostVoted','mostViewed');return false;\">Most Voted</a> | "+
            "<a href='#' onclick=\"Engine_Api_Utility.flip('mostViewed','mostVoted');return false;\">Most Views</a>"+
            "<div id='mostVoted' class='block'>"+
                this._orderByVotes +
            "</div>"+
            "<div id='mostViewed' class='hide'>"+
                this._orderByViews +
            "</div>"+
        "</div>";
        return ret;
    }
};