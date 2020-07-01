(function () {
  const $ = jQuery;

  function addTriggers() {
    $("#PHOTOCAT_cat_number").bind("click keyup mouseup", function () {
      const n = parseInt($("#PHOTOCAT_cat_number").val());
      const labels = $(".photo-cat-option-labels").toArray();
      const values = $(".photo-cat-option-values").toArray();

      for (let i = 0; i < labels.length; i++) {
        const display = i < n ? "inherit" : "none";
        $(labels[i]).css("display", display);
        $(labels[i]).closest("tr").find("th").css("display", display);
        $(values[i]).css("display", display);
        $(values[i]).closest("tr").find("th").css("display", display);
      }
    });
  }

  $(document).ready(addTriggers);
})();
