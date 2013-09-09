var CharacterSelector = (function() {
  var MAXIMUM_RESULTS = 15;
  var lastQuery;
  var searchDelay;

  function init() {
    $("#charname").bind("textchange", function() {
      if (searchDelay) clearTimeout(searchDelay);
      var query = $(this).val();
      lastQuery = query;

      // Clear results
      updateResults([]);

      // Must have at least 3 characters
      if (query.length < 3) {
        if (query) $("#charmin").show();
        $("#charloader").hide();
        return;
      } else {
        $("#charmin").hide();
      }

      $("#charloader").show();

      // Wait 300 milliseconds of inactivity before searching
      searchDelay = setTimeout(function() {
        search(query);
      }, 300);
    });
  }

  // Perform search
  function search(query) {
    $.get("/lodestone/characters?query=" + encodeURIComponent(query), function(results) {
      if (lastQuery != query) return;
      $("#charloader").hide();
      updateResults(results);
    });
  }

  // Update results list
  function updateResults(results) {
    $("#charselect").html("");

    (results.length < MAXIMUM_RESULTS) ? $("#charmany").hide() : $("#charmany").show();

    for (var i = 0; i < results.length && i < MAXIMUM_RESULTS; ++ i) {
      var character = results[i];
      var url = "http://na.finalfantasyxiv.com/lodestone/character/" + encodeURIComponent(character.id);

      var picture = $("<img/>").attr("src", character.picture).attr("width", 30).attr("height", 30);
      picture = $("<a></a>").attr("href", url).attr("target", "_blank").append(picture);
      picture = $("<td></td>").append(picture);

      var name = $("<strong></strong>").text(character.name);
      name = $("<td></td>").append(name);

      var button = $("<button>Link</button>").addClass("btn").addClass("btn-block").addClass("btn-primary").addClass("btn-sm").attr("onclick", "CharacterSelector.select('" + character.id + "', '" + character.name + "')");
      button = $("<td></td>").attr("width", 100).append(button);

      var row = $("<tr></tr>");
      row.append(picture);
      row.append(name);
      row.append(button);

      $("#charselect").append(row);
    }
  }

  // Confirm character selection
  function select(id, name) {
    var confirm = prompt("WARNING: Once you select your character you cannot change it anymore. Type your character name again to confirm.");
    if (!confirm) return;

    if (confirm.toLowerCase() != name.toLowerCase()) {
      alert("Character name did not match");
      return;
    }

    $("#charform [name=character]").val(id);

    // TODO: Submit form
    $("#charform").submit();
  }

  return {
    init: init,
    select: select
  }
})();
