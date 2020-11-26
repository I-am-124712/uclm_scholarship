class SummaryTitlePanel {

    constructor() {
        let $div = $("<div>");

        this.periods = [null, "First", "Second"];
        this.months = months;

        this.$panel = $div.clone();
        // Don't let the 'span' fool you though. They're all just divs
        this.$schoolNameSpan = $div.clone();
        this.$titleSpan = $div.clone();
        this.$periodSpan = $div.clone();
        this.$departmentNameSpan = $div.clone();

        this.schoolNameText = "University of Cebu Lapu-lapu and Mandaue<br>A.C Cortes Ave., Looc, Mandaue City";
        this.titleText = "WORKING SCHOLARS' ALLOWANCE SUMMARY REPORT";
        this.periodText= "";
        this.monthText = "";
        this.schoolYearText = "";
        this.departmentNameText = "";

        // We style every elements in our Title Panel.
        this.$panel.attr("id", "summary-title");
        this.$panel.css({
            'width':'100%',
            'height': 'auto',
            'border-radius': 'inherit',
            'background-color':'white',
            // 'box-shadow': '3px 3px 3px rgba(0,0,0,0.2)',
            'color':'white'
        });

        this.$schoolNameSpan.css({
            'width':'inherit',
            'line-height' : '30px',
            'font-size':'14px',
            'color':'black',
            'text-align':'center'
        });

        this.$titleSpan.css({
            'width':'inherit',
            'line-height' : '30px',
            'font-size':'14px',
            'color':'black',
            'text-align':'center'
        });
        this.$periodSpan.css({
            'width':'inherit',
            'line-height' : '30px',
            'font-size':'14px',
            'color':'black',
            'text-align':'center'
        });
        this.$departmentNameSpan.css({
            'width':'inherit',
            'line-height' : '30px',
            'font-size':'20px',
            'font-weight':'bold',
            'color':'black',
            'text-align':'center'
        });
    }

    setSchoolName(schoolNameText){
        this.schoolNameText = schoolNameText;
        return this;
    }
    setPeriod(periodIndex){
        this.periodIndex = periodIndex;
        this.periodText = this.periods[periodIndex];
        return this;
    }
    setMonth(monthIndex){
        this.monthIndex = monthIndex;
        this.monthText = this.months[monthIndex];
        return this;
    }
    setSchoolYear(schoolYearText){
        if(this.monthIndex < 5)
            this.schoolYearText = schoolYearText.split("-")[1];
        else
            this.schoolYearText = schoolYearText.split("-")[0];
        return this;
    }
    setDepartment(departmentNameText){
        this.departmentNameText = departmentNameText;
        return this;
    }

    /**
     * Wrapper method for setting the information on this Summary Title Panel object.
     * 
     * @param schoolNameText String containing a school name
     * @param periodIndex integer denoting the DTR period selected
     * @param monthIndex integer denoting the DTR month selected
     * @param schoolYearText String containing the currently selected School Year
     * @param departmentNameText String containing a department name 
     */
    set({
        schoolNameText = this.schoolNameText, 
        periodIndex = this.periodIndex,
        monthIndex = this.monthIndex,
        schoolYearText = this.schoolYearText,
        departmentNameText = this.departmentNameText
    })
    {
        this.setSchoolName(schoolNameText);
        this.setPeriod(periodIndex);
        this.setMonth(monthIndex);
        this.setSchoolYear(schoolYearText);
        this.setDepartment(departmentNameText);
        return this;
    }

    prepare(){
        this.$schoolNameSpan.html(this.schoolNameText);
        this.$titleSpan.text(this.titleText);
        this.$periodSpan.text(this.periodText + " Period of " + this.monthText + " " + this.schoolYearText);
        this.$departmentNameSpan.text(this.departmentNameText);

        this.$panel.text("");

        this.$panel.append(this.$schoolNameSpan);
        this.$panel.append(this.$titleSpan);
        this.$panel.append(this.$periodSpan);
        this.$panel.append(this.$departmentNameSpan);

        return this;
    }

    getSummaryTitlePanel(){
        return this.$panel;
    }
}