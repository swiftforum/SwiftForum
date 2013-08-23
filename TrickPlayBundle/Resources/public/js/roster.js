var RosterEditor = (function() {
  function init(allowEdit, availableRanks) {

    // Set selection names
    for (var field in availableRanks) {
      if (!availableRanks[field]) continue;

      var li = $("#editor li[data-role=" + field + "]");
      li.find(".editor-name").text(availableRanks[field]);
      li.data("name", availableRanks[field]);
    }

    // Hide rank changer if not allowed to edit roster
    if (!allowEdit) {
      $("#editor-rankchanger-divider").remove();
      $("#editor-rankchanger").remove();
    }

    // Show dropdown on click
    $(".roster-user").click(function(e) {
      var username = $(this).data("username");
      var editable = $(this).data("editable");
      var role = $(this).data("role");
      var id = $(this).data("id");

      // If the same user is clicked twice, close dropdown
      if (id == $("#editor").data("id") && $("#editor").is(":visible")) {
        $("#editor").hide();
        return;
      }

      $("#editor-username").text(username);

      var editor = $("#editor");
      editor.data("username", username).data("id", id);

      if (editable) {
        $("#editor .editor-sufficient").show();
        $("#editor .editor-insufficient").hide();
      } else {
        $("#editor .editor-insufficient").show();
        $("#editor .editor-sufficient").hide();
      }

      // Hide unavailable ranks
      for (var field in availableRanks) {
        var li = $("#editor li[data-role=" + field + "]");
        if (!availableRanks[field]) li.hide();
      }

      // Highlight the appropriate list item
      $("#editor .editor-sufficient").each(function() {
        var listRole = $(this).data("role");
        if (listRole == role) {
          $(this).find(".editor-icon").addClass("icon-circle").removeClass("icon-circle-blank");
          $(this).addClass("roster-editor-active");
        } else {
          $(this).find(".editor-icon").removeClass("icon-circle").addClass("icon-circle-blank");
          $(this).removeClass("roster-editor-active");
        }
      })

      editor.css("top", e.pageY).css("left", e.pageX).show();
      return false;
    });

    // Hide dropdown on outside click
    $(document).click(function() {
      $("#editor").hide();
    })

    // Perform update on select
    $(".editor-sufficient a").click(function() {
      var id = $("#editor").data("id");
      var username = $("#editor").data("username");
      var roleName = $(this).parent().data("name");
      var role = $(this).parent().data("role");

      if (!confirm("Change " + username + "'s role to " + roleName + "?")) return;
      $("[data-id=" + id + "]").data("role", role);
      // TODO: Really send data over
    })
  }

  return {init: init};
})();
