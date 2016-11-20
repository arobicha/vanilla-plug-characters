<?php if (!defined('APPLICATION')) exit(); ?>

<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">
    <?php echo t($this->Data['PluginDescription']); ?>
</div>
<h3><?php echo t('Templates'); ?></h3>
<?php
    echo $this->Form->open();
    echo $this->Form->errors();

    $templates = Gdn::sql()
                  ->select('*')
                  ->from('SheetTemplates')
                  ->get();

if ($templates->count() > 0 ) :
?>
<ul>
    <?php foreach ($templates->resultArray() as $template) : ?>
      <li><?php echo json_encode($template); ?></li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
    <div class="Info">No Templates found.</div>
<?php endif; ?>

    <button id="" class="Button" style="margin-bottom: 1rem;">New Template</button>

<?php
    echo $this->Form->close('Save');
?>
