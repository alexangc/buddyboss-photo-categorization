(function () {
  const $ = jQuery;

  const pagination = {
    limit: 9,
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

  function refreshPhotos() {
    goToPage(1);
  }

  function goToPage(page) {
    const categories = getSelectedCategories();
    pagination.page = page;

    getPictures(categories, page, pagination.limit).then((response) => {
      const pageCount = Math.ceil(response.data.total_count / pagination.limit);
      populateGallery(response.data.photos.medias);
      displayPagination(page, pageCount);
    });
  }

  $(document).ready(refreshPhotos);
  $(".photocat-filter").change(refreshPhotos);
})();
