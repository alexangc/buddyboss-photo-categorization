<div id="photocat-main-flex-container" class="photocat-main-flex-container">
    {foreach from=$collections item=collection}
    <div class="photocat-collections-parent">
        <div
          class="photocat-collections-center-column"
          onclick='PHOTOCAT.openCollection(
            {$collection->id},
            "{$collection->title}"
          )'>
            {foreach from=$collection->photos item=photo}
            <div
              class="photocat-collections-photo-frame"
              style="background-image: url({$photo->thumb});">
            </div>
            {{/foreach}}
        </div>
        <div
          class="photocat-collection-title"
          onclick='PHOTOCAT.openCollection(
            {$collection->id},
            "{$collection->title}"
          );'
        >
          <b>{$collection->title}</b>
        </div>
    </div>
    {/foreach}
</div>

<template id='photo-box-template'>
  <div class='photocat-photo-frame'>
  </div>
</template>
