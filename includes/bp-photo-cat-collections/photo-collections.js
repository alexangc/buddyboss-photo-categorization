const PHOTOCAT = (($) => {
  async function getPictures(id) {
    return axios.post("/wp-admin/admin-ajax.php?action=get_collection", {
      action: "get_collection",
      id,
    });
  }

  function getPhotoFrameTemplate() {
    const tpl = $("#photo-box-template")[0].content.querySelector("div");
    return document.importNode(tpl, true);
  }

  function addPhotoFrame(picture) {
    const photo_url = picture.thumb;
    const frame = getPhotoFrameTemplate();
    $(frame).append(
      `<img src="${photo_url}" loading="lazy" class="photocat-img"/>`
    );

    document.getElementById("photocat-main-flex-container").appendChild(frame);
    return frame;
  }

  function updateHeaderTitle(newTitle) {
    newTitle && $(".entry-title").html(newTitle);
  }

  function addPreviousPageButtonInHeaderTitle() {
    const prevPage = window.location.origin + window.location.pathname;

    window.onpopstate = () => (window.location = prevPage);

    const prevButton = $(`<span class="previous round">&#8249;</span>`);
    $(prevButton).click(() => {
      window.location = prevPage;
    });
    $(".entry-title").parent().prepend(prevButton);
  }

  function createMozaic(pictures) {
    pictures.forEach((picture) => {
      const frame = addPhotoFrame(picture);
      $(frame).click(() =>
        window.SimpleLightbox.open({
          items: pictures.map((entry) => entry.thumb),
        })
      );
    });
  }

  function openCollection(id, title, pushstate = true) {
    $(".photocat-main-flex-container").html("");
    getPictures(id).then((response) => {
      pushstate &&
        history.pushState(
          { page: 1 },
          "collection",
          // window.location.href.replace(/\/$/, "") + // remove trailing slash
          `?show_collection&collection_id=${id}&collection_title=${title}`
        );
      updateHeaderTitle(title);
      addPreviousPageButtonInHeaderTitle();
      createMozaic(response.data);
    });
  }

  function onReady() {
    const params = new URLSearchParams(window.location.href);
    const id = params.get("collection_id");
    const title = params.get("collection_title");

    // If the user entered a collection's access URL, we jump straight
    // to that collection's content.
    if (id && title) {
      openCollection(id, title, false);
    }
  }

  $(document).ready(onReady);

  return {
    openCollection,
  };
})(jQuery);
