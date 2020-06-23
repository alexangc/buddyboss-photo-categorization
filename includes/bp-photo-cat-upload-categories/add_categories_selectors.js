(function (categories) {
  const $ = jQuery; // The '$' shortcut doesn't work by default in Wordpress

  function makeSelect(id, name, placeholder, options) {
    let choices = `<option selected> --- ${placeholder} --- </option>\n`;
    options.forEach((opt) => {
      choices += `<option name="${opt}" value="${opt}">${opt}</option>\n`;
    });

    return `<span>
              <select id="${id}" name="${name}" class="PHOTOCAT_select">
                ${choices}
              </select>
            </span>`;
  }

  function embed_selectors(data) {
    const selectors = [];

    Object.keys(data).forEach((category) => {
      const id = `PHOTOCAT_categories_${category}`;
      const placeholder = data[category].label;
      const options = data[category].options;
      selectors.push(makeSelect(id, id, placeholder, options));
    });

    let template = '<div id="PHOTOCAT_selectors">\n';
    selectors.forEach((selector) => (template += `${selector}\n`));
    template += "<div>\n";

    $(template).insertAfter($("header.bb-model-header"));
  }

  function onMediaSubmit() {
    const medias = bp.Nouveau.Media.dropzone_media;
    const categories = $(".PHOTOCAT_select")
      .toArray()
      .filter((el) => $(el).find(":selected").attr("name"))
      .map((select) => ({
        label: $(select).attr("name"),
        value: $(select).val(),
      }));

    medias.forEach((media) => (media.categories = categories));

    // TODO: better display or configuration
    // TODO: translation system ?
    if (categories.length === 0) {
      alert("You must select at least one category for this upload !");
      event.stopImmediatePropagation();
    }
  }

  function add_submit_hook() {
    const submit_button = document.getElementById("bp-media-submit");
    // `useCapture` must be set to `true`, to bypass BuddyBoss's event handlers
    submit_button.addEventListener("click", onMediaSubmit, true);
  }

  embed_selectors(categories);
  add_submit_hook();
})(PHOTOCAT_categories_data);
