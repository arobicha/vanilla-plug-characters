<?php
if (!defined('APPLICATION')) { exit(); }
$Session = Gdn::session();

// echo json_encode($this->Data('character'));
$character = $this->Data('character');
function NullCheck($data, $key)
{
  return $data ? (property_exists($data, $key) ? $data->$key : '') : "";
}

?>
<?php if ( empty($this->Data("Error")) ) : ?>
<h2><?php echo (NullCheck($character, 'CharacterId') != '') ? "Edit Character" : "New Character"; ?></h2>
<?php if ( !empty($this->Data("success"))) : ?>
  <div class="character-edit-notification-success">
    <?php echo $this->Data("success"); ?>
  </div>
<?php elseif (!empty($this->Data("warning"))) : ?>
  <div class="character-edit-notification-warning">
    <?php echo $this->Data("warning"); ?>
  </div>
<?php endif; ?>
<div id='DiscussionForm' class="DiscussionForm">
  <p>To use your character, once submitted, type: <code>&lt;div class='dialog' data-char='Handle'&gt;Dialog here!&lt;/div&gt;</code> into a post. Magic takes care of the rest!</p>
  <form class='character-edit' method="POST">
    <input type='hidden' name='CharacterId' value='<?php echo NullCheck($character, 'CharacterId'); ?>' />
    <label for='Name'>Name</label>
    <input type='text'   name='Name' value='<?php echo NullCheck($character, 'Name'); ?>' />
    <label for='Slug'>Handle (A-Z, a-z, 0-9, ., -, _)</label>
    <input type='text'   name='Slug' value='<?php echo NullCheck($character, 'Slug'); ?>' pattern='[A-Za-z0-9-._]+' />
    <label for='Game'>Game (A-Z, a-z, 0-9, ., -, _)</label>
    <input type='text'   name='Game' value='<?php echo  NullCheck($character, 'Game'); ?>' pattern='[A-Za-z0-9-._]+' />
    <label for='ImgThumb'>Thumbnail Image URL</label>
    <input type='text'   name='ImgThumb' value='<?php echo NullCheck($character, 'ImgThumb'); ?>' placeholder='http://' />
    <label for="Sheet">Sheet URL</label>
    <input type="text"   name='Sheet' value='<?php echo NullCheck($character, 'Sheet'); ?>' placeholder='http://' />
    <label for='SheetData'>Description</label>
    <textarea type='text'   name='SheetData'><?php echo NullCheck($character, 'SheetData'); ?></textarea>
    <input class="button" type='submit' value='submit' />
  </form>
<?php else : ?>
<div id='DiscussionForm' class="DiscussionForm">
  <h2>Oops...</h2>
  <p><?php echo $this->Data("Error"); ?></p>
</div>
<?php endif; ?>
</div>
