class MinimalistTable {


    constructor(){
        // base table
        this.$table = $('<table>');

        // base div
        this.$div = $("<div>");

        // base table row
        this.$tr = $('<tr>');

        // base table header
        this.$th = $("<th>");

        // base table data
        this.$td = $('<td>');

        // add appropriate classes for each element
        this.$table.addClass('table-minimalist');
        this.$th.addClass('table-minimalist-header');
        this.$td.addClass('table-minimalist-data');

        // For insertion of new td rows
        this.$dataRowCollection = this.$div.clone();
        this.$lastDataRow = null;

        // this Table object's Header
        this.$header = this.$tr.clone();
        this.$header.attr("id","header-row");
    }

    addHeader(headerLabels = {}){
        for(let x in headerLabels){
            let $newHeader = this.$th.clone();
            // newHeader.val(x);
            $newHeader.text(headerLabels[x]);
            this.$header.append($newHeader);
        }
        return this;
    }
    addDataRow(data = {}, id = null){
        let newRow = this.$tr.clone();
        newRow.attr("id", id);

        for(let x in data){
            let newData = this.$td.clone();
            newData.attr("id", x);
            newData.text(data[x]);
            newRow.append(newData);
        }

        this.$dataRowCollection.append(newRow);
        return this;
    }

    resetDataRows(){
        // this.$table.next('tr + th').nextUntil().remove();
        this.$dataRowCollection = this.$div.clone();
        return this;
    }

    prepare(){
        this.$table.html('');

        this.$table.append(this.$header);
        this.$table.append(this.$dataRowCollection.children());

        return this;
    }


    getTable(){
        return this.$table;
    }
}