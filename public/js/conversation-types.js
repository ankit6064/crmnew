/**
 * Conversation Types & Sub-types handler
 */
var subOptions = {
    'Follow up': [
        {value: 'Follow-up Call', label: 'Call'},
        {value: 'Follow-up Email/Info Requested', label: 'Email'},
        {value: 'Follow-up Text', label: 'Text'}
    ],
    'Declined': [
        {value: 'Declined - No Opportunity', label: 'No Opportunity'},
        {value: 'Declined - DNC', label: 'DNC'},
        {value: 'Declined - Not Right Party', label: 'Not Right Party'},
        {value: 'Declined - Left the Organization', label: 'Left the Organization'},
        {value: 'Declined - Reference Shared', label: 'Reference Shared'}
    ],
    'Meeting Setup': [
        {value: 'Meeting Set-up', label: 'Meeting Setup'}
    ]
};

window.subOptions = subOptions;

/**
 * Update Sub Conversation Type dropdown based on parent select value
 */
function updateSubConversationType(parentSel, childSel, colSel) {
    var $parent = $(parentSel || ( $('#reminder_for').length ? '#reminder_for' : '#quick_reminder_for' ));
    var $child = $(childSel || ( $('#sub_reminder_for').length ? '#sub_reminder_for' : '#quick_reminder_for_child' ));
    var $col = $(colSel || ( $('#sub_conversation_col').length ? '#sub_conversation_col' : '#reminder_for_child_col' ));
    
    var parentVal = $parent.val();
    $child.empty().append('<option value="">Choose Sub Type</option>');
    
    if (parentVal && subOptions[parentVal]) {
        $.each(subOptions[parentVal], function(i, opt) {
            $child.append('<option value="' + opt.value + '">' + opt.label + '</option>');
        });
        if (subOptions[parentVal].length > 0) {
            $child.val(subOptions[parentVal][0].value);
        }
        $col.show();
    } else {
        $col.hide();
    }
}
window.updateSubConversationType = updateSubConversationType;

/**
 * Update Dial Note Sub Conversation Type dropdown
 */
function updateDialSubConversationType(parentSel, childSel, colSel) {
    var $parent = $(parentSel || '#dial_parent_reminder_for');
    var $child = $(childSel || '#dial_reminder_for');
    var $col = $(colSel || ( $('#dial_sub_conversation_col').length ? '#dial_sub_conversation_col' : '#dial_reminder_for_child_col' ));
    
    var parentVal = $parent.val();
    $child.empty().append('<option value="">Choose Sub Type</option>');
    
    if (parentVal && subOptions[parentVal]) {
        $.each(subOptions[parentVal], function(i, opt) {
            $child.append('<option value="' + opt.value + '">' + opt.label + '</option>');
        });
        if (subOptions[parentVal].length > 0) {
            $child.val(subOptions[parentVal][0].value);
        }
        $col.show();
    } else {
        $col.hide();
    }
}
window.updateDialSubConversationType = updateDialSubConversationType;

/**
 * Handle type checking for Quick Note (Callback vs Reminder datetime)
 */
function checkQuickType(parentSel) {
    var $parent = $(parentSel || ( $('#reminder_for').length ? '#reminder_for' : '#quick_reminder_for' ));
    var val = $parent.val();
    if (val === 'Callback') {
        $('#reminderdatetime').hide();
        $('#callbackdatetime').show();
    } else if (!val) {
        $('#reminderdatetime').hide();
        $('#callbackdatetime').hide();
    } else {
        $('#reminderdatetime').show();
        $('#callbackdatetime').hide();
    }
}
window.checkQuickType = checkQuickType;
window.checktype = checkQuickType;

/**
 * Handle type checking for Dial Note
 */
function checkdialtype(parentSel) {
    var $parent = $(parentSel || '#dial_parent_reminder_for');
    var val = $parent.val();
    if (val === 'Callback') {
        $('#dial_reminderdatetime').hide();
        $('#dial_callbackdatetime').show();
    } else if (!val) {
        $('#dial_reminderdatetime').hide();
        $('#dial_callbackdatetime').hide();
    } else {
        $('#dial_reminderdatetime').show();
        $('#dial_callbackdatetime').hide();
    }
}
window.checkdialtype = checkdialtype;
window.checkDialParentType = checkdialtype;

