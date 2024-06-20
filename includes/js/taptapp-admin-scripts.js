jQuery(document).ready(function($) {
    // Tab Navigation
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        var tabId = $(this).attr('href');
        $('.tab-content').removeClass('active');
        $(tabId).addClass('active');
    });

    // Placeholder Copy to Clipboard
    $('.insert-placeholder').on('click', function() {
        var placeholder = $(this).data('placeholder');
        console.log('Placeholder clicked:', placeholder);
        navigator.clipboard.writeText(placeholder).then(function() {
            console.log('Copied to clipboard:', placeholder);
            alert('Copied to clipboard: ' + placeholder);
        }, function(err) {
            console.error('Could not copy text:', err);
        });
    });

    // Optional: Drag and Drop functionality
    $('.insert-placeholder').draggable({
        helper: "clone"
    });

    $('textarea').droppable({
        accept: '.insert-placeholder',
        drop: function(event, ui) {
            var text = ui.helper.text();
            var cursorPos = $(this).prop('selectionStart');
            var v = $(this).val();
            var textBefore = v.substring(0, cursorPos);
            var textAfter = v.substring(cursorPos, v.length);
            $(this).val(textBefore + text + textAfter);
        }
    });
});
