$.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    //redraw to account for filtering and sorting
    // concept here is that (for client side) there is a row got inserted at the end (for an add)
    // or when a record was modified it could be in the middle of the table
    // that is probably not supposed to be there - due to filtering / sorting
    // so we need to re process filtering and sorting
    // BUT - if it is server side - then this should be handled by the server - so skip this step
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;
        oSettings.oApi._fnReDraw(oSettings);
        //iDisplayStart has been reset to zero - so lets change it back
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }
     
    //draw the 'current' page
    oSettings.oApi._fnDraw(oSettings);
};

$.fn.dataTableExt.oApi.fnAddDataAndDisplay = function ( oSettings, aData )
{
    /* Add the data */
    var iAdded = this.oApi._fnAddData( oSettings, aData );
    var nAdded = oSettings.aoData[ iAdded ].nTr;
     
    /* Need to re-filter and re-sort the table to get positioning correct, not perfect
     * as this will actually redraw the table on screen, but the update should be so fast (and
     * possibly not alter what is already on display) that the user will not notice
     */
    this.oApi._fnReDraw( oSettings );
     
    /* Find it's position in the table */
    var iPos = -1;
    for( var i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
    {
        if( oSettings.aoData[ oSettings.aiDisplay[i] ].nTr == nAdded )
        {
            iPos = i;
            break;
        }
    }
     
    /* Get starting point, taking account of paging */
    if( iPos >= 0 )
    {
        oSettings._iDisplayStart = ( Math.floor(i / oSettings._iDisplayLength) ) * oSettings._iDisplayLength;
        this.oApi._fnCalculateEnd( oSettings );
    }
     
    this.oApi._fnDraw( oSettings );
    return {
        "nTr": nAdded,
        "iPos": iAdded
    };
}