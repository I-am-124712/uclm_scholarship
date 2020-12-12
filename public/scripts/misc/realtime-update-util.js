
let idForUpdate = '';
let paramsForUpdate = '';

setInterval(()=>{
    reloadPage(idForUpdate, paramsForUpdate);
}, 5000);

const reloadPage = (id = '', params = '') =>{
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