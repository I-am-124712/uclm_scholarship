
let idForUpdate = '';
let paramsForUpdate = '';

// We will store all our actions for the realtime update
// feature. We will iterate every one of them whenever we can and
// individually perform them in our loop.
let actionToPerform = {};

// Shortcut method for pushing our function and parameters to
// perform inside our setInterval loop.
const addForPerform = (functionRef, paramObj) => {
    index = functionRef.name;
    actionToPerform[index] = {
        function: functionRef,
        params: paramObj
    };
}

// Included by default will be a reload on a certain part of the page
// which will be refreshed through ajax. 
const reloadPage = ({id = '', params = ''}) =>{
    let requestReload = window.location.href;
    let idToLoad = '';
    
    if(id !== '')
        idToLoad = "#" + id;
    else return;

    $.ajax({
        url: requestReload,
        dataType: 'html',
        data: params,
        method: 'post',
        success : res => {
            let resultHtml = $('' + res + '');
            let forContents = resultHtml.find('div' + idToLoad);
            $('div' + idToLoad).html(forContents);
        },
        err: err => {
            console.log(err.responseText);
        }
    })
};

addForPerform(reloadPage, {id: idForUpdate, params: paramsForUpdate});

setInterval(()=>{
    for(let x in actionToPerform){
        let func = actionToPerform[x].function;
        let args = actionToPerform[x].params;
        func(args);
    }
}, 5000);

