<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $info->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $info->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Infos'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="infos form large-9 medium-8 columns content">
    <?= $this->Form->create($info) ?>
    <fieldset>
        <legend><?= __('Edit Info') ?></legend>
        <?php
            echo $this->Form->control('data');
            echo $this->Form->control('value');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
