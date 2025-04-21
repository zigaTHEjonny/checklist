<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1><?=$name?></h1>
<button id="delete-all-btn" title="Delete All">
    🗑️
</button>


<?php if (session()->get('success')): ?>
    <div class="success text-box">
    <?= session->get('success') ?>
</div>
<?php endif; ?>

<div class="input-group">
      <input type="text" id="new-item-input" placeholder="Add a new item..." />
      <button id="add-button">Add</button>
    </div>


<ul id="unchecked-list">
    <?php foreach ($unchecked_items as $item) : ?>
        <?= view_cell('\App\Libraries\Checklist::listItem', $item) ?>
    <?php endforeach; ?>
</ul>

<button id="toggle-completed" class="toggle-btn">Show Completed (<?php count($checked_items) ?>)</button>
<ul id="checked-list" style="display: none;">
<?php foreach ($checked_items as $item) : ?>
        <?= view_cell('\App\Libraries\Checklist::listItem', $item) ?>
    <?php endforeach; ?>
</ul>


<?= $this->endSection() ?>
