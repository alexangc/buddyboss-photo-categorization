const PHOTOCAT = (($) => {

  async function getPictures(id) {
    return axios.post("/wp-admin/admin-ajax.php?action=get_collection", {
      action: "get_collection",
      id,
    });
  }

  function openCollection(id) {
    getPictures(id).then((response) => {
      window.SimpleLightbox.open({
        items: response.data.map((entry) => entry.thumb),
      });
    });
  }

  return {
    openCollection,
  };
})(jQuery);
