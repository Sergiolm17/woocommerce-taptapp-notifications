jQuery(document).ready(function ($) {
  // Keep track of the last focused textarea
  var lastFocusedTextarea;

  $("textarea").on("focus", function () {
    lastFocusedTextarea = this;
  });

  // Placeholder Copy to Cursor Position
  $(".insert-placeholder").on("click", function () {
    var placeholder = $(this).data("placeholder");

    if (lastFocusedTextarea) {
      var $textarea = $(lastFocusedTextarea);
      var cursorPos = $textarea.prop("selectionStart");
      var text = $textarea.val();
      var textBefore = text.substring(0, cursorPos);
      var textAfter = text.substring(cursorPos, text.length);
      $textarea.val(textBefore + placeholder + textAfter);
      $textarea.focus();
      $textarea[0].setSelectionRange(
        cursorPos + placeholder.length,
        cursorPos + placeholder.length
      );
    }
  });

  // Optional: Drag and Drop functionality
  $(".insert-placeholder").draggable({
    helper: "clone",
  });

  $("textarea").droppable({
    accept: ".insert-placeholder",
    drop: function (event, ui) {
      var text = ui.helper.text();
      var cursorPos = $(this).prop("selectionStart");
      var v = $(this).val();
      var textBefore = v.substring(0, cursorPos);
      var textAfter = v.substring(cursorPos, v.length);
      $(this).val(textBefore + text + textAfter);
    },
  });
});
