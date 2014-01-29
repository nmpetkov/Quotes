/**
 * create the onload function to enable the respective functions
 *
 */
Event.observe(window, 'load', quotes_init_check, false);

function quotes_init_check()
{
    if($('quotes_multicategory_filter')) {
        quotes_filter_init(); 
    }
}

/**
 * Admin panel functions
 */
function quotes_filter_init()
{
    Event.observe('quotes_property', 'change', quotes_property_onchange, false);
    quotes_property_onchange();
    //$('quotes_multicategory_filter').style.display = 'inline';
}

function quotes_property_onchange()
{
    $$('div#quotes_category_selectors select').each(function(select){
        select.hide();
    });
    var id = "quotes_"+$('quotes_property').value+"_category";
    $(id).show();
}

/**
 * Toggle active/inactive status
 */
function setstatus(qid, status)
{
    ajaxindicator = document.getElementById("statusajaxind_"+qid);
    ajaxindicator.style.display = "inline";

    var pars = {qid: qid, status: status};
    new Zikula.Ajax.Request("ajax.php?module=Quotes&func=setstatus",
        {parameters: pars, onComplete: setstatus_response});
}
function setstatus_response(req)
{
    if (!req.isSuccess()) {
        Zikula.showajaxerror(req.getMessage());
        return;
    }
    var data = req.getData();
    
    if (data.alert) {
        alert(data.alert);
    }

    ajaxindicator = document.getElementById("statusajaxind_"+data.qid);
    ajaxindicator.style.display = "none";

    elementActive = document.getElementById("statusactive_"+data.qid);
    elementInactive = document.getElementById("statusinactive_"+data.qid);
    if (elementActive && elementInactive) {
        if (data.status == 1) {
            elementActive.style.display = "block";
            elementInactive.style.display = "none";
        } else {
            elementActive.style.display = "none";
            elementInactive.style.display = "block";
        }
    }
}