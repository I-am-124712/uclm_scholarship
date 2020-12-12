 // styling for drawer
 let $drawer = $(".drawer"); 
 $drawer.css({
     'background' : "rgb(240,240,240)",
     'border-radius': '20px',
     'width' : "225px",
     'height': 'auto',
     'padding': '10px',
     'box-shadow': '2px 2px  5px rgb(0,0,0,0.25)',
     'margin': '0px 20px 20px 0px',
     'float' : 'left',
     'display' : 'block',
 });
 $drawer.children().css({
     'color' : 'black',
     'font-size': '14px'
 });
 $drawer.children('label').css({
     'color' : 'rgb(30, 98, 223)',
     'font-weight' : 'bold'
 });
 $drawer.children('select').css({
     'background-color' : 'white',
     'border' : 'unset',
     'box-shadow': '3px 3px 3px rgba(0,0,0,0.1)',
     'color' : 'black',
     'font-size': '14px'
 })
 $drawer.children('select').children("option").css({
     'background-color': 'white',
     'color': 'black'
 })
 $('#btn-load').css({
     'margin-top' : "15px",
     'width' : '100%' 
 });
 // initialize custom style for Save and PDF button
 $("button#btn-save").css({
     'width' : '100%',
     'margin-top': '10px',
     'float' : 'left',
     'background-color' : 'inherit'
 });
 // add a mouse-over listener for them buttons.
 // For Load Button
 $('#btn-load').css({
     'background-color': 'rgb(43, 117, 255)',
     'color': 'white',
     'margin-top' : "15px",
     'width' : '100%',
 });
 $("button#btn-load").mouseenter(function(){
     $(this).css({
         'background-color' : 'rgb(90, 175, 231)'
     });
 }).mouseleave(function(){
     $(this).css({
         'background-color' : 'rgb(43, 117, 255)'
     });
 });

 // For Transparent buttons:
 $("button#btn-transparent").css({
     'width' : '100%',
     'margin-top': '10px',
     'float' : 'left',
     'font-size' : '14px',
     'color' : 'black',
     'background-color' : 'inherit'
 });
 $("button#btn-transparent").mouseenter(function(){
     $(this).css({
         'background-color' : 'rgb(90,150,255)',
         'color' : 'white'
     });
 }).mouseleave(function(){
     $(this).css({
         'background-color' : 'inherit',
         'color' : 'black'
     });
 });