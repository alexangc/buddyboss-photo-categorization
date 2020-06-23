<div class="photocat-main-flex-container">
    {foreach from=$collections item=collection}
    <div class="photocat-collections-parent">
        <div
          class="photocat-collections-center-column"
          onclick='PHOTOCAT.openCollection({$collection->id})'>
            {foreach from=$collection->photos item=photo}
            <div
              class="photocat-collections-photo-frame"
              style="background-image: url({$photo->thumb});">
            </div>
            {{/foreach}}
        </div>
        <div
          class="photocat-collection-title"
          onclick='PHOTOCAT.openCollection({$collection->id})'>
          <b>{$collection->title}</b>
        </div>
    </div>
    {/foreach}
</div>
