<li>
    <div class="left">
        <input class="checkbox_input" type="checkbox" <?php if($checked > 0) echo "checked"; ?> id="<?=$li_id?>" />
        <label for="<?=$li_id?>"><span class="<?php if($checked > 0) echo "completed"; ?>"><?=$task_name?></span></label>
    </div>
    <button class="delete-btn" data-id="<?=$li_id?>">✕</button>
</li>