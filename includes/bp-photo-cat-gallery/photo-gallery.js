(function () {
  const $ = jQuery;

  const pagination = {
    limit: 9,
  };

  const state = {
    medias: [],
    user: {
      collections: [],
    },
  };

  function getPhotoFrameTemplate() {
    const tpl = $("#photo-box-template")[0].content.querySelector("div");
    return document.importNode(tpl, true);
  }

  function getSelectedCategories() {
    return $(".photocat-filter")
      .toArray()
      .filter((f) => $(f).prop("checked"))
      .map((f) => $(f).val());
  }

  function addPhotoFrame(media) {
    const photo_url = media.attachment_data.full;
    const box = getPhotoFrameTemplate();

    $(box).attr("style", `background-image: url("${photo_url}");`);
    $(box).attr("data-media-id", media.id);

    document.getElementById("gallery").appendChild(box);
  }

  function populateGallery(medias) {
    $("#gallery").html("");
    medias.forEach(addPhotoFrame);
  }

  async function getPictures(categories, page, limit) {
    return axios.post("/wp-admin/admin-ajax.php?action=get_photos", {
      action: "get_photos",
      categories,
      page,
      limit,
    });
  }

  function makePaginatioItem(page, isLink) {
    const style = isLink ? "link" : "";
    const link = $(`<span class=${style}>${page}</span><span>&nbsp;</span>`);

    if (isLink) {
      $(link).click(() => {
        window.scrollTo(0, 0);
        goToPage(page);
      });
    }
    return link;
  }

  function displayPagination(currPage, pageCount) {
    const pageLinks = $("#pagination");
    pageLinks.html("");

    if (pageCount <= 1) {
      return;
    }

    // Before current page (included)
    if (currPage <= 4) {
      for (let i = 1; i <= currPage; i++) {
        pageLinks.append(makePaginatioItem(i, i !== currPage));
      }
    } else {
      pageLinks.append(makePaginatioItem(1, true));
      pageLinks.append("<span>..&nbsp;");
      pageLinks.append(makePaginatioItem(currPage - 1, true));
      pageLinks.append(makePaginatioItem(currPage, false));
    }

    // After current page (excluded)
    if (pageCount - currPage <= 4) {
      for (let i = currPage + 1; i <= pageCount; i++) {
        pageLinks.append(makePaginatioItem(i, i !== currPage));
      }
    } else {
      pageLinks.append(makePaginatioItem(currPage + 1, true));
      pageLinks.append("<span>..&nbsp;");
      pageLinks.append(makePaginatioItem(pageCount, true));
    }
  }

  function getCurrentUserCollections() {
    return axios.post("/wp-admin/admin-ajax.php?action=get_self_collections", {
      action: "get_self_collections",
    });
  }

  function refreshPhotos() {
    goToPage(1);
  }

  function tagPhotoWithCollection(frame, collection) {
    const selector = $(frame).find("div.selector");
    const title = collection.title.slice(0, 25);
    $(selector).html(title);
    $(selector).removeClass("selector");
    $(selector).addClass("selector-tagged");

    $(frame).find(".save_button").css("display", "none");
  }

  function populateCollectionSelectors(medias, collections) {
    $(".photocat-photo-frame")
      .toArray()
      .forEach((frame) => {
        const mediaId = parseInt($(frame).attr("data-media-id"));
        const select = $(frame).find("select.selector");
        const media = medias.find((m) => m.id === mediaId);
        const collection =
          media &&
          media.collection &&
          collections.find((c) => c.id === media.collection);

        if (collection) {
          tagPhotoWithCollection(frame, collection);
          return;
        }

        // else
        const createCollection = document.importNode(
          $(
            "#photo-box-template-create-collection-option"
          )[0].content.querySelector("option"),
          true
        );
        $(createCollection).click(() => {
          openCreateCollectionModal(mediaId);
          const defaultOption = $(select).find(".dash-default");
          $(defaultOption).attr("selected", true);
        });
        select.append(createCollection);

        const sizeLimit = 30;
        state.user.collections.forEach((collection) => {
          const id = parseInt(collection.id);
          const title =
            `${collection.title.slice(0, sizeLimit)}` +
            (collection.title.length > sizeLimit ? `...` : ``);

          select.append(`<option value=${id}>${title}</option>`);
        });
      });
  }

  async function savePhotoInCollection(event) {
    const target = event.currentTarget;
    const frame = $(target).closest(".photocat-photo-frame");

    const mediaId = parseInt(
      $(target).closest(".photocat-photo-frame").attr("data-media-id")
    );
    const media = state.medias.find((m) => m.id === mediaId);

    const selected = $(target)
      .closest(".photocat-bottom-panel")
      .find("select")
      .val();
    const collection = state.user.collections.find(
      (c) => parseInt(c.id) === parseInt(selected)
    );

    if (!media || !collection) {
      return;
    }

    const data = {
      media_id: media.id,
      collecton_id: collection.id,
    };

    axios
      .post("/wp-admin/admin-ajax.php?action=save_photo", {
        action: "save_photo",
        ...data,
      })
      .then((response) => {
        if (response.data && response.data.done) {
          tagPhotoWithCollection(frame, collection);
        }
      });
  }

  function addSaveTriggers() {
    $(".photocat-bottom-panel .save_button").click(savePhotoInCollection);
  }

  function goToPage(page) {
    const categories = getSelectedCategories();
    pagination.page = page;

    getPictures(categories, page, pagination.limit)
      .then((response) => {
        state.medias = response.data.photos.medias;
        const pageCount = Math.ceil(
          response.data.total_count / pagination.limit
        );
        populateGallery(response.data.photos.medias);
        displayPagination(page, pageCount);
      })
      .then(() => getCurrentUserCollections())
      .then((response) => {
        state.user.collections = response.data;
        populateCollectionSelectors(state.medias, state.user.collections);
        addSaveTriggers();
      });
  }

  function openCreateCollectionModal(mediaId) {
    const modal = $("#create-collection-modal");
    const id = parseInt(mediaId);
    const media = state.medias.find((media) => media.id == id);

    modal.css("display", "flex");
    modal
      .find(".photocat-photo-frame")
      .css("background-image", `url(${media.attachment_data.thumb})`);
  }

  $(document).ready(refreshPhotos);
  $(".photocat-filter").change(refreshPhotos);
})();
