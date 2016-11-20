<?php if (!defined('APPLICATION')) { exit(); }
$Session = Gdn::session();
$characters = $this->Data('characters');
?>
<div>
  <div id="DiscussionForm" class="DiscussionForm">
      <h1>Characters</h1>
      <ul class="DataList Discussions">
        <?php
        foreach ( $characters as $character )
        {
          ?>
          <li class='Item ItemDiscussion CharacterListItem' id='character-<?php echo $character['CharacterId']; ?>'>
            <div class='character-meta-container'>
              <img class='character-thumb' style='background-image: url("<?php echo $character['ImgThumb']; ?>")' />
            </div>
            <div class="CharacterData">
              <h2><?php echo $character['Name']; ?> <small>[<?php echo $character['Slug'] . " in <a href='/discussions/characters/" . $character['Game'] . "'>" . $character['Game']; ?></a>]</small></h2>
              <p class="description"><?php echo $character['SheetData']; ?></p>
            </div>
            <span class='character-menu'>
              <?php if ( $character['Sheet'] != '' ) : ?>
                <a class='button' href='<?php echo $character['Sheet']; ?>'><i class="fa fa-file-text-o" aria-hidden="true"></i> Sheet</a>
              <?php endif; ?>
              <?php if ( is_object($Session->User) && (($character['Owner'] == $Session->User->UserID) || ($Session->User->Admin == '1')) ) : ?>
                <a class='button character-edit' href='/discussions/characters/edit/<?php echo $character['CharacterId']; ?>'><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                <a class='button character-delete' data-id='<?php echo $character['CharacterId']; ?>' data-slug='<?php echo $character['Slug']; ?>' href='#'><i class="fa fa-times" aria-hidden="true"></i> Delete</a>
              <?php endif; ?>
            </span>
          </li>
        <?php
        }
        ?>
      </ul>
  </div>
  <div class="character-list-menu">
    <div class="character-menu">
        <a class="button" href='/discussions/characters/new/<?echo $this->Data('Game'); ?>'><i class="fa fa-plus" aria-hidden="true"></i> New</a>
    </div>
  </div>
</div>
