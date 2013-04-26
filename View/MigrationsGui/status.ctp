<?php 
    $bShowApplied = false;
    $_pendingTotal = 0; 
?>
<?php foreach ($data as $type=>$mappings) : ?>
    <?php 
        $_list = array();
        $_pendingCnt = 0;
    ?>
    
    <?php foreach ($mappings as $m) : ?>

        <?php 
            $_isApplied = !empty($m['migrated']);
            $_pendingCnt += $_isApplied ? 0 : 1; 
        ?>
        
        <?php if ($_isApplied && !$bShowApplied) { continue; }?>
        
        <?php $this->assign('migration_entry', ''); ?>
        <?php $this->start('migration_entry'); ?>
        
        <?php $liAttr = array('class'=>array($_isApplied ? 'applied muted' : 'not-applied')); ?>
        
        <?php echo $this->Html->tag('li', null, $liAttr); ?>
            <?php echo Inflector::humanize(Inflector::underscore($m['class']))?> <small class="muted"><?php echo $this->Time->format('d.m.Y H:i:s', $m['version'])?></small>
            <div>
                <?php if ($_isApplied) : ?>
                    <?php echo $this->Html->link('Undo', array('action'=>'run', $type, $m['version'], 'down'), array('icon'=>'undo')); ?>
                <?php else : ?>
                    <?php echo $this->Html->link('Apply', array('action'=>'run', $type, $m['version'], 'up'), array('icon'=>'play')); ?>
                <?php endif; ?>
            </div>
        </li>
        <?php echo $this->end(); ?>
        <?php $_list[] = $this->fetch('migration_entry'); ?>
    <?php endforeach; ?>
    
    <?php $_pendingTotal += $_pendingCnt; ?>
    
    <?php if (!empty($_list)) : ?>        
        <h3>
            <?php echo h($type); ?>
            <?php if ($_pendingCnt > 1) : ?>
                <small>
                    <?php echo $this->Html->link('Apply All', array('action'=>'runall', $type,), array('icon'=>'play')); ?>
                </small>
            <?php endif; ?>
        </h3>
        
        <ul class="nav">
            <?php echo implode("\n", $_list); ?>
        </ul>
        
    <?php endif; ?>
    
<?php endforeach;?>