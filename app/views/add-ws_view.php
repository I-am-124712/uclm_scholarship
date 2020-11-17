
<div class="modal-overlay" id="modal-overlay-add-ws">
    <div class="modal-panel" id="panel-add-ws"> 
        <h1 class="modal-header"><b>Add Working Scholar</b></h1>
        <div id="form-panel" style="padding-left:30px;padding-right:30px;padding-bottom:10px;padding-top:20px">
            <form action="/uclm_scholarship/working_scholars/add" method="post">
                <p>
                    <center class="label" id="dep-assigned">
                        Department : 
                        <uli class="label" id="label-deptname" style="font-size:20px">
                            <b><?=isset($args)? $args->get_fields()['departmentName']:''?></b>
                        </uli>
                    </center>
                </p>
                <input hidden id="deptId" name="deptId" type="text" value="<?=$args->get_fields()['deptId']?>">
                <label id="form-label" style="padding-bottom:5px; color:white">ID Number 
                    <div id="err-msg-idnum" 
                         style="float:right;
                                font-size: 15px;
                                width:fit-content;
                                height:15px;
                                border-radius:20px;
                                color:rgb(255,100,100)"></div>
                    <input spellcheck="false" class="textbox" id="idnumber" type="text" name="idnumber">
                </label>
                <label id="form-label" style="padding-bottom:5px; color:white">Last Name 
                    <div id="err-msg-lname" 
                         style="float:right;
                                font-size: 15px;
                                width:fit-content;
                                height:15px;
                                border-radius:20px;
                                color:rgb(255,100,100)"></div>
                    <input spellcheck="false" class="textbox" id="lname" type="text" name="lname">
                </label>
                <label id="form-label" style="padding-bottom:5px; color:white">First Name 
                    <div id="err-msg-fname" 
                         style="float:right;
                                font-size: 15px;
                                width:fit-content;
                                height:15px;
                                border-radius:20px;
                                color:rgb(255,100,100)"></div>
                    <input spellcheck="false" class="textbox" id="fname" type="text" name="fname">
                </label>
                <label id="form-label" style="padding-bottom:5px; color:white">Date of Hire 
                    <div id="err-msg-datehired" 
                         style="float:right;
                                font-size: 15px;
                                width:fit-content;
                                height:15px;
                                border-radius:20px;
                                color:rgb(255,100,100)"></div>
                    <input class="textbox" id="date_of_hire" type="date" name="date_of_hire">
                </label>
                <label id="form-label" style="padding-bottom:5px; color:white">Course 
                    <input spellcheck="false" class="textbox" id="course" type="text" name="course">
                </label>
            </form>
            <div class="container">
                <button class="button-solid round" 
                        id="form-button" 
                        style="height:40px"
                        onclick="save()">
                    Save
                </button>
                <button class="button-flashing round" 
                        id="form-button-transparent" 
                        style="height:40px"
                        onclick="closeModal()">
                    Cancel
                </button>
            </div>
        </div>
