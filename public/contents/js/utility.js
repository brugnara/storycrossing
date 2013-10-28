function getSelectValue(selectObj) {
    // get the index of the selected option
    var idx = selectObj.selectedIndex;
    // get the value of the selected option
    var which = selectObj.options[idx].value;
    return which;
}
var baseUrl = '';
var Engine_Api_Utility = {
    flip : function(idToShow,idToHide) {
        dojo.byId(idToShow).setAttribute("class","block");
        dojo.byId(idToHide).setAttribute("class","hide");
    },
    checkCharsCount : function (textArea, min, max, titleKo, titleOk, colorKo, colorOk) {
        if (typeof colorOk == 'undefined') {
            colorOk = 'lightGreen';
        }
        if (typeof colorKo == 'undefined') {
            colorKo = 'lightCoral';
        }
        if (typeof titleOk == 'undefined') {
            titleOk = 'Text lenght is perfect!';
        }
        if (typeof titleKo == 'undefined') {
            titleKo = 'The perfect story has at least 100 and a max of 1000 chars, but you can write long as you want.';
        }
        //controlla quanti caratteri ha una textarea e cambia il colore in base all'input
        ta = dojo.byId(textArea);
        if (!ta) {
            return;
        }
        var l;
        try {
            l = ta.value.length;
        } catch (e) {
            return;
        }
        var color, title;
        if (l > min && l < max) {
            color = colorOk;
            title = titleOk;
        } else {
            color = colorKo;
            title = titleKo;
        }
        ta.setAttribute("style","background-color:"+color);
        ta.setAttribute("title",title+" ("+ l +")");
    }
};