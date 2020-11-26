
class MessageStrip {
    
    constructor(){
        // Initialize container elements
        this.$strip = $('<div>');
        this.$imageContainer = $('<div>');
        this.$imageIcon = $('<div>');
        this.$message = $('<span>');
        this.$timeStrip = $('<span>');
        this.$userLabel = $('<a>');

        // Style container elements
        this.$strip.css({
            'background-color': 'inherit',
            'cursor':'unset',
            'width': '100%',
            'height':'60px',
            'padding':'6px 0px',
            'transition-duration':'.3s',
            'border-bottom': '1px solid rgb(200,200,200)'
        });
        this.$imageContainer.css({
            'border-radius':'100%',
            'background-color':'rgb(0, 81, 255)',
            'float': 'left',
            'margin' : '5px 10px',
            'border': '5px solid inherit'
        });
        this.$imageIcon.css({
            'width' : '30px',
            'height': '30px',
            'background-size':'cover',
            'background-repeat':'no-repeat',
        });
        this.$userLabel.css({
            'color':'rgb(0, 81, 255)',
            'cursor':'pointer',
            'float':'left',
            'font-size':'14px',
            'font-weight':'bolder',
            'text-decoration':'none',
        });
        this.$message.css({
            'cursor':'default',
            'float':'left',
            'width': '90%',
            'height': 'auto',
            'margin':'5px 0px',
            'white-space': 'nowrap',
            'overflow': 'hidden',
            'text-overflow': 'ellipsis',
            'font-size':'14px',
            'color':'black'
        });
        this.$timeStrip.css({
            'float':'left',
            'height': '10px',
            'font-size': '10px',
            'color':'rgb(100,100,100)',
            'text-align':'left',
            'margin-left':'50px'
        });

        // Add event listeners
        this.$strip.hover(function(){
            $(this).css({
                'background-color': 'rgb(180,180,180)',
            });
        }, function(){
            $(this).css({
                'background-color': 'inherit',
            });
        });

        this.$userLabel.hover(function(){
            $(this).css({
                "text-decoration":"underline"
            });
        }, function(){
            $(this).css({
                "text-decoration":"none"
            });
        });


    }

    setIcon = iconURL =>{
        this.iconURL = iconURL;
        return this;
    }
    setUserLabel = userName =>{
        this.userName = userName;
        return this;
    }
    setMessage = msg =>{
        this.messageText = msg;
        return this;
    }
    setTime = timeStr => {
        this.timeString = timeStr;
        return this;
    }
    setLink = url =>{
        this.url = url;
        return this;
    }


    finalize = ()=>{
        this.$userLabel.text(this.userName);
        this.$userLabel.attr('href', this.url);
        this.$timeStrip.text(this.timeString);

        this.$imageIcon.css(
            'background-image', "url(" + this.iconURL + ")"
        );
        this.$imageContainer.append(this.$imageIcon);

        this.$message.append(" ");
        this.$message.append(this.messageText);
        
        this.$strip.append(this.$imageContainer);
        this.$strip.append(this.$userLabel);
        this.$strip.append(this.$message);
        this.$strip.append(this.$timeStrip);
        this.$strip.click(()=>{
            location.href = this.url;
        });
    }

    getMessagesStrip = _=>{
        this.finalize();
        return this.$strip;
    }
}