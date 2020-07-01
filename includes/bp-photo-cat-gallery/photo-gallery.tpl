<div class='photocat-main-flex-container'>
  <!-- Category filters -->
  <div class='photocat-left-column'>
    <ul>
      {foreach from=$categories item=cat}
        <li>{$cat['label']}</li>
        <ul>
        {foreach from=$cat['options'] item=option}
          {assign var='tag' value=PHOTOCAT_tagify($option)}
          <li>
            <input type='checkbox' id='{$tag}' class='photocat-filter' value='{$tag}'>
            <label for='{$tag}'> {$option}</label>
          </li>
        {/foreach}
        </ul>
        <hr>
      {/foreach}
    </ul>
  </div>
  <!-- Photo gallery + pagination -->
  <div class='photocat-center-column'>
    <div id='gallery'> </div>
    <div id='pagination'>
    </div>
  </div>
</div>

<template id='photo-box-template'>
  <div class='photocat-photo-frame'>
    <div class="photocat-bottom-panel">
      <div class="selector">
        <select class="selector">
          <option class='dash-default'> - </option>
        </select>
      </div>
      <button class="save-button">
        {__('Save', 'buddyboss-photo-categorization')}
      </button>
    </div>
  </div>
</template>

<template id='photo-box-template-create-collection-option'>
  <option>
    + {__('Create collection', 'buddyboss-photo-categorization')}
  </option>
</template>

<div id="create-collection-modal" class="dialogue">
  <div class='photocat-photo-frame'>
  </div>
  <div class="dialogue-info">
    <div class="dialogue-title">
      <p>{__('Create My Collection', 'buddyboss-photo-categorization')}</p>
      <span class="photocat-close-modal"></span>
    </div>
    <div width="100%"> <hr> </div>
    <textarea
      class="dialogue-textarea"
      placeholder="{__('Collection name', 'buddyboss-photo-categorization')}"
    >
    </textarea>
    <button class="dialogue-button">
      {__('Create', 'buddyboss-photo-categorization')}
    </button>
  </div>
</div>
