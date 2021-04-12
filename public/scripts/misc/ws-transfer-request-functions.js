const submitTransferRequest = ()=>{
    // retrieve Transfer Request text.
    let text = $('textarea#transfer-request-text').val();
    text = text.replace(/\r?\n/g, '<br>');
    
    let params = 'req=true&request_text=' + text;

    $.ajax({
        method: 'POST',
        data: params,
        url: '/uclm_scholarship/working_scholars/createTransferRequest',
        success: res => {
            console.log(res);
            $('textarea#transfer-request-text').val('')
        }
    })
}
