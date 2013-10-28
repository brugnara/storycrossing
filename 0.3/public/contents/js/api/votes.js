var Engine_Api_Votes = {
    _basePath : '',
    _resultDiv : null,
    _divs : [],
    _timeout : null,
    //
    _currentIndex : 0,
    //
    getVotes : function(idDiv,basePath,id) {
        this._basePath = basePath;
        //chiamo la pagina per avere i voti
        var uri = this._basePath+'/books/ajax/getvotes/id/'+id;
        this._resultDiv = dojo.byId(idDiv);
        this._setLoader();
        var currObj = this;
        dojo.xhr.get({
            url: uri,
            // The success handler
            load: function(response) {
                Engine_Api_Votes._prepareVotes(response);
                currObj.startRoll();
            },
            // The error handler
            error: function() {
                currObj._resultDiv.innerHTML = "Error! Retry in few seconds.";
            },
            // The complete handler
            handle: function() {
                hasBeenSent = true;
            }
        });
    },
    startRoll : function() {
        this._currentIndex = 0;
        this._roll();
    },
    stopRoll : function() {
        if (this._timeout != null) {
            clearTimeout(this._timeout);
        }
    },
    _roll : function() {
        //se ho almeno 3 elementi, li faccio rollare: A + B sono sempre visibili. In C ho il prossimo.
        //A scompare, B va al posto d A e C al posto di B con effetto fading. In A carico prossimo elemento.
        if (typeof this._divs[this._currentIndex] == "undefined") {
            this._resultDiv.innerHTML = "<b>Not yet voted.</b>";
            return;
        }
        this._resultDiv.innerHTML = this._divs[this._currentIndex];
        if (typeof this._divs[this._currentIndex + 1] == 'undefined') {
            this._currentIndex = 0;
        } else {
            this._currentIndex++;
        }
        this._timeout = setTimeout('Engine_Api_Votes._roll()',4000);
    },
    _setLoader : function() {
        this._resultDiv.innerHTML = Engine_Api_Images.getBouncingBall();
    },
    _prepareVotes : function(votes) {
        votes = eval(votes);
        //comment
        //vote
        //user
        for (var i in votes) {
            this._divs.push(this._getDiv(votes[i].user, votes[i].vote, votes[i].comment));
        }
    },
    _getDiv : function(user,vote,comment) {
        var ret =
        "<div class='button singleVote'>"+
            "<table>"+
                "<tr>"+
                    "<th>"+user+" has voted "+vote+"</th>"+
                "</tr>"+
                "<tr>"+
                    "<td>..."+comment+"</td>"+
                "</tr>"+
            "</table>"+
        "</div>";
        return ret;
    }
}