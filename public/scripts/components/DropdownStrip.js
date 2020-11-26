/**
 * Class for Dropdown Strips used for the Dropdown Panel
 */
class DropdownStrip {

    constructor(){
        let $div = $('<div>');
        let $span = $('<span>');
        let $p = $('<p>');

        this.$strip = $div.clone();
        this.$image = $div.clone();
        this.$par = $p.clone();
        this.$profile = $div.clone();
        this.$info = $div.clone();
        this.$time = $span.clone();

        $div = $span = null;

    }

    setInfo(text){
        this.$par.text(text);
        return this;
    }

    setTime(timeStr){
        this.$time.text(timeStr);
        return this;
    }

    setValue(val){
        this.$strip.val(val);
        return this;
    }

    finalize(){
        this.$strip.addClass('drop-strip');
        this.$image.addClass('drop-img');
        this.$profile.addClass('drop-profile');
        this.$info.addClass('drop-info');
        this.$time.addClass('drop-time');

        this.$image.append(this.$profile);
        this.$info.append(this.$par);
        this.$info.append(this.$time);

        this.$strip.append(this.$image);
        this.$strip.append(this.$info);

        return this;
    }

    getStrip(){
        return this.$strip;
    }
}