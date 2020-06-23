<div class="photocat-main-flex-container">
    {foreach from=$collections item=collection}
    <div class="photocat-collections-parent">
        <div class="photocat-collections-center-column">
            {foreach from=$collection->photos item=photo}
            <div
              class="photocat-collections-photo-frame"
              style="background-image: url({$photo->thumb});">
            </div>
            {{/foreach}}
        </div>
        <div class="photocat-collection-title">
          <b>{$collection->title}</b>
        </div>
    </div>
    {/foreach}
</div>
