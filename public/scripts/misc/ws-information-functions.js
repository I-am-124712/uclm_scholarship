
    const domParser = new DOMParser();
    const schedTypeNames = ["REG","SPC"];
    let domObj = null;
    let isEditScheduleMode = false;
    let schedTypeName = "REG";
    let selectedScheduleId = -1;



    // clears the selected Days toggle buttons. Used for initializing the page
    const clearDaysToggleButtons = _=>
    { 
        $(".form-flat#days-panel")
        .children("#day-of-week")
        .each(function(){
            $(this).removeClass("active");
        });
        $("#tin").val("08:00");
        $("#tout").val("09:00");
        $("#spc-date").val(null);
        selectedScheduleId = -1;
    }

    // Exit Edit Mode
    const exitEditMode = src =>{
        isEditScheduleMode = false;
        src.siblings(".button-tab").removeClass("active");
        src.addClass("active");
        $("#is-edit-mode").text("");
        clearDaysToggleButtons();
    }

    // loads the schedules in the Schedule panel
    const loadSched = function(){
        schedType = "schedType=" + schedTypeNames[$(".button-tab.active#sched-type").index()];
        semester = 'semester=' + ($(".button-tab.active#semester").index() + 1);
        schoolYear = $("select#school-year").serialize();
        idnumber = $('#selected-id').serialize();
        args = schedType + "&" + semester + '&' + schoolYear + '&' + idnumber;
        url = "/uclm_scholarship/working_scholars/view_schedules";

        response = $.post({
            url : url,
            data: args,
            dataType: 'html',
            async: false
        }).responseText;


        domObj = domParser.parseFromString(response,'text/html');
        table = domObj.getElementById("sched-data");
    
        $("table#sched-data").replaceWith(table);
    };

    /* Edit Schedule */
    const editSchedule = ($src)=>{
        $(function(){
            // clean (reset) all Days toggle button first...
            clearDaysToggleButtons();


            if(!isEditScheduleMode){
                isEditScheduleMode = true;
            }
            $("#is-edit-mode").text("Edit Mode");
            scheduleId = $src.val();
            selectedScheduleId = scheduleId;

            // we will create a function for formatting our time string
            // to comply with the format required for the Time input fields.
            // Note that this is not usable for general cases of formatted
            // Time strings and only accepts Time strings with format "hh:mm am|pm".
            // Put short, just for the purpose of this functionality.
            //
            // Here's a smiley 😂
            const formatTime = (timeString)=>{
                let timeParts = timeString.replace(" ",":").split(':');
                let formattedString = '';
                for(let i=0; i<2; ++i){
                    timeParts[i] = parseInt(timeParts[i]);
                }
                let hour, minute;

                switch(timeParts[2]){
                    case "am":
                    case "AM":
                    case "aM":
                    case "Am":
                        hour = (timeParts[0] < 10) ? "0" + timeParts[0] : "" + timeParts[0];
                        minute = (timeParts[1] < 10) ? "0" + timeParts[1] : "" + timeParts[1];
                        break;
                    case "pm":
                    case "PM":
                    case "pM":
                    case "Pm":
                        hour = timeParts[0]==12? "" + 12 : "" + (12 + timeParts[0]);
                        minute = (timeParts[1] < 10) ? "0" + timeParts[1] : "" + timeParts[1];
                        break;
                }
                formattedString = hour + ":" + minute + ":00";

                return formattedString;
            }
            // Similar to the function above, we will make yet another local formatter
            // this time for the Date string. Just to comply with the date format required
            // by the HTML input element. Luckily this came out shorter than the time formatter.
            // Again, another smiley 😅
            const formatDate = dateString => {
                let dateParts = dateString.split("/");
                let month = (parseInt(dateParts[0]) < 10 ? "0"+dateParts[0]:dateParts[0]);
                let day = (parseInt(dateParts[1]) < 10 ? "0"+dateParts[1]:dateParts[1]);
                let year = dateParts[2];

                return year + "-" + month + "-" + day;
            }

            // we will then prepare our form for edit.
            // Start by extracting the Times-in and -out
            // from the calling Edit Button's parent row...
            let timeSched = $src.parents().find("div#time-" + scheduleId).text().trim().split(' - ');
            let timeIn = formatTime(timeSched[0]);
            let timeOut = formatTime(timeSched[1]);
            let schedDay = $src.parents().find("div#day-" + scheduleId).text().trim().split(' - ')[1];


            // then set the Time-in and Time-out fields with the selected value...
            $("input#tin").val(timeIn);
            $("input#tout").val(timeOut);

            switch(schedTypeName){
                case "REG": // For Regular Schedules
                    let days = schedDay.split(', ');

                    // activate all Days toggle button that matches the selected schedule's days...
                    if(days.length > 0){
                        for(let i=0; i<days.length; ++i){
                            $("#day-of-week[value=" + days[i] + "]").addClass("active");
                        }
                    }
                    break;
                case "SPC": // For Specific Schedules

                    // we simply have to update the Specific Date input field to
                    // the date of the selected schedule.
                    
                    $("input#spc-date").val(formatDate(schedDay));
                    break;
                default:
                    return;
            }
        });
    };


    /* Delete a selected Schedule */
    const deleteSchedule = (schedId)=>{
        scheduleId = "scheduleId="+schedId;
        $.ajax({
            url: '/uclm_scholarship/working_scholars/delete_schedule',
            type: 'post',
            data: scheduleId,
            success: function(res){
                console.log(res);
            }
        });
        loadSched();
    };

    /* confirmative Deletion function */
    const confirmDelete = schedId =>
        confirm("Are you sure you want to delete this Schedule?")? deleteSchedule(schedId):"";

    /* Switch the controls between WeekDay Selector and Date Picker */
    const scheduleType = ()=>{
        let label = ["SELECT DAYS","ENTER A SPECIFIC DATE"];
        source = $(".button-tab.active#sched-type");
        $("#day-label").text(label[source.index()]);
        $(".form-flat").children(".form-flat#days-panel").hide();
        $(".form-flat").children(".form-flat#days-panel").eq(source.index()).show();
    };


    // This area is for saving the schedule. We will find a better solution I swear.
    //
    // Edit as of November 3, 2020:
    //      This will be the main process for adding a schedule,
    // and will be used for editing it as well. Basically, an "Edit Schedule" action
    // is supposed to be an UPDATE SQL statement, instead it will be a combination 
    // of DELETE and INSERT. The reason being the aggregated schedule days for Regular 
    // Schedules sharing similar times-in and -out but with different Schedule Day(s)  
    // (eg. MWF 8AM-12PM, these schedules share the same sched_id and has to be deleted  
    // entirely because we shouldn't have two or more records that share the same  
    // sched_id but with different times-in and -out).
    //
    const saveSched = function(){
        
        $.ajax({
            type: 'GET',
            url: '/uclm_scholarship/working_scholars/schedule_index',
            dataType: 'JSON',
            success: function(data){
                // var schedTypeName = schedTypeNames[$(".button-tab.active#sched-type").index()];
                var schedType = "schedType=" + schedTypeName;
                var schoolYear = $("select#school-year").serialize();
                var semester = "semester=" + ($(".button-tab.active#semester").index()+1);
                var idnumber = $("#selected-id").serialize();
                var tin = $("input#tin").serialize();
                var tout = $("input#tout").serialize();
                var scheduleId = "schedule_id=" + (selectedScheduleId == -1? (data.schedId+1):selectedScheduleId);

                // We are in Edit mode? Delete the record first before saving.
                if(isEditScheduleMode)
                    deleteSchedule(selectedScheduleId);

                switch(schedTypeName){
                    // Regular Schedule
                    case "REG": 
                        // what we do is loop through every active day-of-week Element and add these schedules
                        $(".form-flat#days-panel")
                        .children("#day-of-week")
                        .each(function(){
                            if($(this).hasClass("active")){
                                schedDay = "schedDay=" + $(this).text();

                                params = scheduleId + "&"
                                        + schedType + "&"
                                        + schoolYear + "&"
                                        + semester + "&"
                                        + idnumber + "&"
                                        + schedDay + "&"
                                        + tin + "&"
                                        + tout;

                                $.post({
                                    url: '/uclm_scholarship/working_scholars/add_schedule',
                                    data: params,
                                    dataType: 'html',
                                    success: _=>{
                                        console.log(_);
                                    }
                                });
                            }
                        });
                        loadSched();
                        break;

                    // Specific Schedule
                    case "SPC":
                        date = new Date($("input#spc-date").val());
                        schedDay = "schedDay=" + date.toLocaleDateString();

                        params = scheduleId + "&"
                                + schedType + "&"
                                + schoolYear + "&"
                                + semester + "&"
                                + idnumber + "&"
                                + schedDay + "&"
                                + tin + "&"
                                + tout;

                        $.post({
                            url: '/uclm_scholarship/working_scholars/add_schedule',
                            dataType: 'html',
                            data: params,
                            success: _=>{
                                console.log(_.responseText);
                            },
                            error: err=>{
                                console.log(err.responseText);
                            }
                        });


                        loadSched();
                        break;
                    default:
                        
                }

                // Exit Edit mode if we are editing. We don't want to continually
                // edit the Schedule entry once we're done editing.
                _ = isEditScheduleMode? exitEditMode($(this)):'';

            },
            error: function(e){
                console.log(e.responseText);
            }
        });
    };  


    $(function(){

        /// Default selected tabs and items ///
        $("#sched-type").children(".button-tab").eq(0).addClass('active');
        $("#sched-sem").children(".button-tab").eq(0).addClass('active');
        
        /// load 1st Sem Schedule automatically
        scheduleType();
        loadSched();

        /// click a tab button. In this page, we make sure that the user
        /// will confirm changes when editing schedules before leaving...
        $(".button-tab").click(function(){
            if(isEditScheduleMode){
                if(confirm("Are you sure you want to leave Edit mode? Changes will not be saved.")){
                    exitEditMode($(this));
                }
            }
            else{
                $(this).siblings(".button-tab").removeClass("active");
                $(this).addClass("active");
            }
        });

        /// load records based on selected school year and semester;
        $(".button-tab#semester").click(function(){
            if(!isEditScheduleMode){
                loadSched();
            }
        });

        /// choose what type of schedule to save
        $(".button-tab#sched-type").click(function(){
            if(!isEditScheduleMode){
                schedTypeName = schedTypeNames[$(this).index()];
                $("#is-edit-mode").text("");
                scheduleType();
                loadSched();
            }
        });

        /// for Day of Week toggle buttons
        $(".button-solid.round-toggle#day-of-week").click(function(){
            _ = $(this).hasClass("active")? 
                        $(this).removeClass("active"):
                        $(this).addClass("active");
        });

        /// For WS Edit Information ///
        $("#save-edit").click(function(){
            let args = $("#information").serialize();

            // buffered page. we'll use this to query the edit form
            // and replace it in our current document without
            // reloading the page

            response = $.post({
                url: "/uclm_scholarship/working_scholars/update",
                data: args,
                dataType: 'html',
                async : false
            }).responseText;
            // console.log(response);

            domObj = domParser.parseFromString(response,'text/html');

            infoForm = domObj.getElementById("ws-information");


            $("div#ws-information").empty();
            $("div#ws-information").replaceWith(infoForm);
            $(this).text('Saved');
            $(this).delay(3000).fadeIn(500,function(){
                $(this).text('Edit Information');
            });
            $("#edit-status").delay(3000).fadeOut(500);

        });
        $('button#save-sched').click(saveSched);

    });