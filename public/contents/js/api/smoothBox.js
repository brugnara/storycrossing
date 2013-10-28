var Engine_Api_SmootBox = {
    _id : 'smoothWindow',
    _idContent : 'smoothContent',
    open : function(uri) {
        var myDiv = dojo.byId(this._id);
        var myFrame = dojo.byId(this._idContent);
        this.clean();
        //
        myDiv.setAttribute("class","visible");
        myFrame.setAttribute("class","visible");
        //apro url
        myFrame.setAttribute("src",uri);
    },
    close : function() {
        this.clean();
        var myDiv = dojo.byId(this._id);
        var myFrame = dojo.byId(this._idContent);
        myDiv.setAttribute("class","hide");
        myFrame.setAttribute("class","hide");
    },
    clean : function() {
        //pulisco iframe
        try {
            var myFrame = dojo.byId(this._idContent);
            myFrame.setAttribute("src",'');
            var frameDoc = myFrame.contentDocument || myFrame.contentWindow.document;
            frameDoc.removeChild(frameDoc.documentElement);
        } catch(e) {
            //alert(e.message);
        }
    }
};

