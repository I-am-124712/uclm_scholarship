
const dropdown = _=>{
    __ = $('#dropdown').attr('isClosed')?
        $('#dropdown').slideDown(300).removeAttr('isClosed'):
        $('#dropdown').slideUp(300).attr('isClosed', true);
};


const prepareSidebar = ()=>{
    $('#dropdown').attr('isClosed',true).toggle();
    $("#dropdown").css({
        "width": "100%",
        "background-color": "rgb(60,60,60)"
    });
    $("#dropdown").children("li").css({
        "text-align" : "left",
        "margin-left": "auto",
        "margin-right": "auto",
        "padding-left": "25%",
        "padding-top" : "5px",
        "position": "relative",
        "height" : "25px"
    });
    $('#dropdown').children('li').children('a').css({
        "text-decoration":"none",
        "color":"white",
        "font-size" : "12px"
    }).mouseenter(function(){
        $(this).parent().css({
            "background-color": "rgb(100,100,100)"
        });
    }).mouseleave(function(){
        $(this).parent().css({
            "background-color": "rgb(60,60,60)"
        });
    })
};