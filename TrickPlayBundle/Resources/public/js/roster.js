var Roster = (function() {
  function show(klass) {
    var formattedClass = klass.charAt(0).toUpperCase() + klass.slice(1);
    var topEntries = [];
    var lowEntries = [];

    // Get data
    $("tr.ff-" + klass).each(function() {
      var portrait = $(this).find("img").attr("src");
      var name = $(this).find("td").eq(1).text();
      var level = $(this).find("span.fficon-" + klass).parent().text().match(/[0-9]+/)[0];

      // Clean data
      name = name.replace(/(^\s+)|(\s+$)/g, "");
      level = parseInt(level.replace(/(^\s+)|(\s+$)/g, ""));

      var entry = {
        name: name,
        portrait: portrait,
        level: level
      }

      if (level < 50) {
        lowEntries.push(entry);
      } else {
        topEntries.push(entry);
      }
    })

    // Sort by level, and then by name
    topEntries.sort(sortEntries);
    lowEntries.sort(sortEntries);

    // Fill rows (top entries)
    $("#top-entries").html("");
    $.map(topEntries, function(entry) {
      var row = $("<tr></tr>");
      row.append($("<td></td>").append($("<img width='20' height='20'/>").attr("src", entry.portrait)));
      row.append($("<td></td>").append($("<strong></strong>").text(entry.name)));
      row.append($("<td></td>").html(entry.level + " " + formattedClass));

      $("#top-entries").append(row);
    });

    // Fill rows (low entries)
    $("#low-entries").html("");
    $.map(lowEntries, function(entry) {
      var row = $("<tr></tr>");
      row.append($("<td></td>").append($("<img width='20' height='20'/>").attr("src", entry.portrait)));
      row.append($("<td></td>").append($("<strong></strong>").text(entry.name)));
      row.append($("<td></td>").html(entry.level + " " + formattedClass));

      $("#low-entries").append(row);
    });

    // Set title
    var totalEntries = topEntries.length + lowEntries.length;
    $("#browser .modal-title").html("<span class='fficon-" + klass + "'></span> " + (totalEntries) + " " + formattedClass + "s");

    $("#browser").modal();
  }

  function sortEntries(first, second) {
    var levelComparison = second.level - first.level;
    var nameComparison = second.name - first.name;
    return levelComparison || nameComparison;
  }

  return {show: show};
})();
