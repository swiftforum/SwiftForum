var Editor = (function() {
  function init(allowEdit) {
    if (!allowEdit) return;

    var backup;
    var htmlBackup;

    $("#frontpage-edit").click(function() {
      $(this).addClass("disabled");

      $("#editor").show();
      backup = $("#editor textarea").val();
      htmlBackup = $("#frontpage-content").html();
    });

    // Live preview
    $("textarea").keyup(function() {
      var content = $(this).val();
      $("#frontpage-content").html(markdown.toHTML(content));
    });

    // Cancel
    $("#editor-cancel").click(function() {
      $("#frontpage-edit").removeClass("disabled");
      $("#editor textarea").val(backup);
      $("#frontpage-content").html(htmlBackup);
      $("#editor").hide();
    });

    // Save changes
    $("#editor-save").click(function() {
      var markdown = $("#editor textarea").val();
      $("#frontpage-edit").removeClass("disabled");
      $("#editor").hide();

      $.post("/frontpage", {markdown: markdown}).done(function(result) {
        // Update timestamp display
        $("#frontpage-timestamp-user").attr("href", result.lastEditor.url);
        $("#frontpage-timestamp-user").text(result.lastEditor.username);
        $("#frontpage-timestamp-time").text(result.lastEdit.date);
        $("#frontpage-timestamp").show();
      }).fail(function() {
        alert("Server error. Try again?");
      });
    });
  }

  return {init: init};
})();
