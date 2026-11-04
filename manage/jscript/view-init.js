$(function() {
    $('a[rel*=facebox]').facebox({ closefunc:popup_modal, loadingImage : 'jscript/facebox/loading.gif', closeImage : 'jscript/facebox/closelabel.gif' });
    chstatus();

    $("#event_popup").click(chstatus);
    $("#event_link").click(chstatus);
    $("#pop_fix").click(chstatus);
    $("#pop_image").click(chstatus);
});
