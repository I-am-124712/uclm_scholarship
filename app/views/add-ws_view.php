
<div class="modal-overlay">
    <div class="modal-panel" id="panel-add-ws"> 
        <h1 class="modal-header"><b>Add Working Scholar</b></h1>
        <div id="form-panel" style="padding-left:30px;padding-right:30px;padding-bottom:10px;padding-top:20px">
            <form action="/uclm_scholarship/working_scholars/add" method="post">
                <p>
                    <center class="label" id="dep-assigned">
                        Department : 
                        <uli class="label" id="label-deptname" style="font-size:24px">
                            <b><?=isset($args)? $args->get_fields()['departmentName']:''?></b>
                        </uli>
                    </center>
                </p>
                <input hidden id="deptId" name="deptId" type="text" value="<?=$args->get_fields()['deptId']?>">
                <label id="form-label" style="padding-bottom:5px">ID Number 
                    <input spellcheck="false" class="textbox" id="idnumber" type="text" name="idnumber">
                    <div id="err-msg-idnum" 
                         style="position:relative;width:fit-content;height:20px;border-radius:20px;color:rgb(255,100,100)">
                        <?=Messages::dump('err_idnum')?></div>
                </label>
                <label id="form-label" style="padding-bottom:5px">Last Name 
                    <input spellcheck="false" class="textbox" id="lname" type="text" name="lname">
                    <div id="err-msg-idnum" 
                         style="position:relative;width:fit-content;height:20px;border-radius:20px;color:rgb(255,100,100)">
                         <?=Messages::dump('err_lname')?></div>
                </label>
                <label id="form-label" style="padding-bottom:5px">First Name 
                    <input spellcheck="false" class="textbox" id="fname" type="text" name="fname">
                    <div id="err-msg-idnum" 
                         style="position:relative;width:fit-content;height:20px;border-radius:20px;color:rgb(255,100,100)">
                         <?=Messages::dump('err_fname')?></div>
                </label>
                <label id="form-label" style="padding-bottom:5px">Date of Hire 
                    <input class="textbox" id="date_of_hire" type="date" name="date_of_hire">
                    <div id="err-msg-idnum" 
                         style="position:relative;width:fit-content;height:20px;border-radius:20px;color:rgb(255,100,100)">
                         <?=Messages::dump('err_date')?></div>
                </label>
            </form>
            <div class="container">
                <button class="button-solid round" 
                        id="form-button" 
                        style="height:40px"
                        onclick="save_ws()">
                    Save
                </button>
                <button class="button-flashing round" 
                        id="form-button-transparent" 
                        style="height:40px"
                        onclick="clearTargetHTML('for-popups')">
                    Cancel
                </button>
            </div>
        </div>