
    const addWorkingScholars = (departmentId)=>{
        url = "/uclm_scholarship/working_scholars/add_ws/"+departmentId;
        $("#for-popups").load(url+" .modal-overlay");
        $("#for-popups").removeAttr("hidden");
    }
    const closeModal = ()=>{
        $("#for-popups").text("");
    };

    const save = ()=>{
        url = "/uclm_scholarship/working_scholars/add";
        params = $('form').serialize();
        console.log(params);

        // callback constants
        const success = (data)=>{
            console.log(data);

            $("#err-msg-idnum").text(data.err_idnum);
            $("#err-msg-lname").text(data.err_lname);
            $("#err-msg-fname").text(data.err_fname);
            $("#err-msg-course").text(data.err_course);

            if(data.success){
                closeModal();
                setTimeout(() => {
                    location.href = '/uclm_scholarship/dash/ws?allow_edit';
                }, 100);
            }
        };
        const err = (data)=>{
            console.log(data);
        };

        // Time to add WS here.
        $.post({
            url: url,
            async: true,
            data: params,
            dataType: 'JSON',
            success: success,
            error : err
        });

    }

    const deleteWorkingScholar = idnumber => {
        if(confirm('Are you sure you want to delete this WS?')){
            httpRequestExternal('GET','/uclm_scholarship/working_scholars/delete?idnumber='+idnumber);
            setTimeout(()=>{ location.href = '/uclm_scholarship/dash/ws?allow_edit'; },60);
        }
    }