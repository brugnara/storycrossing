var Engine_Api_Popup = {
    close : function() {
        parent.location.href = parent.location.href;
        setTimeout('Engine_Api_Popup._hide()',1000);
    },
    _hide : function() {
        try {
            parent.document.getElementById('smoothContent').setAttribute("class","hide");
            parent.document.getElementById('smoothWindow').setAttribute("class","hide");
            //
            parent.document.getElementById('smoothContent').innerHTML = '';
        } catch(e) {}
    }
};