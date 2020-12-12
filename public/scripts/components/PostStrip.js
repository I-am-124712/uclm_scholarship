class PostStrip {

    constructor(){
        let div = $("<div>");
        let span = $("<span>");
        let a = $('<a>');

        // Main Strip
        this.mainStrip = div.clone();

        // Strip to contain the profile pic, username and date/time
        this.profileInfoStrip = div.clone();

        // Profile container
        this.profileContainer = div.clone();
        this.profilePic = div.clone();

        // Profile name
        this.profileName = div.clone();
        this.$profileLink = a.clone();

        // Date/Time span
        this.dateTime = div.clone();

        // Message container
        this.message = div.clone();

        // Initialize texts
        this.profileNameText = "";
        this.dateTimeText = "";
        this.messateText = "";

        // Add attributes
        this.mainStrip.attr('id', 'main-strip');
        this.profileInfoStrip.attr('id', 'profile-info-strip');
        this.profileContainer.attr('id', 'profile-container');
        this.profilePic.attr('id', 'profile-pic');
        this.$profileLink.attr('id', 'profile-link');
        this.profileName.attr('id', 'profile-name');
        this.dateTime.attr('id', 'date-time');
        this.message.attr('id', 'message');


        // Style each components...
        this.styleComponents();
        this.finalize();
    }

    styleComponents(){
        this.mainStrip.css({
            'width': 'auto',
            'height': 'auto',
            'border-bottom': '1px solid rgb(200,200,200)',
            'padding': '15px',
        });

        this.profileInfoStrip.css({
            'width' : 'auto',
            'height': '50px',
            'padding': '0px 15px',
        });

        this.profileContainer.css({
            'width' : '45px',
            'height': '45px',
            'margin': '0px 5px',
            'float' :'left',
        });

        this.$profileLink.css({
            'float':'left',
            'font-weight':'bolder',
        })

        this.profilePic.css({
            'width' : '45px',
            'height': '45px',
            'background-color': 'rgb(0, 64, 184)',
            'background-repeat': 'no-repeat',
            'background-size': 'cover',
            'border-radius' : '100%',
        });

        this.profileName.css({
            'color' : 'rgb(0, 162, 255)',
            'font-weight' : 'bold',
            'float': 'left',
            'margin': '4px 0px',
            'width': 'calc(100% - 100px)',
        });
        this.dateTime.css({
            'font-size' : '12px',
            'float': 'left',
            'margin': 'auto 0px',
            'width': 'calc(100% - 100px)',
        });
        this.message.css({
            'padding': '0 15px'
        })

        return this;
    }

    finalize(){
        // Clear every nodes
        this.mainStrip.text("");

        // set the link
        this.$profileLink.attr('href', this.hrefText);

        // set the component's texts
        this.$profileLink.html(this.profileNameText);
        this.profileName.html(this.$profileLink);
        this.dateTime.html(this.dateTimeText);
        this.message.text(this.messageText);

        // Start adding all components
        this.profileContainer.append(this.profilePic);

        this.profileInfoStrip.append(this.profileContainer);
        this.profileInfoStrip.append(this.profileName);
        this.profileInfoStrip.append(this.dateTime);

        this.mainStrip.append(this.profileInfoStrip);
        this.mainStrip.append(this.message);

        // Event listener for our Anchor element
        this.$profileLink.hover(function(){
            $(this).css({
                "text-decoration": "underline"
            });
        }, function(){
            $(this).css({
                "text-decoration": "none"
            });
        });


        return this;
    }

    setProfileName(profileNameText){
        this.profileNameText = profileNameText;
        return this;
    }

    setProfilePic(picUrl){
        this.profilePic.css({
            'background-image': 'url(' + picUrl + ")"
        });
        return this;
    }

    setHref(url = ""){
        this.hrefText = url;
        return this;
    }

    setDateTime(dateTimeText){
        this.dateTimeText = dateTimeText;
        return this;
    }

    setMessage(messageText){
        this.messageText = messageText;
        return this;
    }

    set({profileNameText, picUrl, url, dateTimeText, messageText}){
        this.setProfileName(profileNameText);
        this.setProfilePic(picUrl);
        this.setHref(url);
        this.setDateTime(dateTimeText);
        this.setMessage(messageText);
        return this;
    }

    getPostStrip(){
        return this.mainStrip.clone();
    }
}