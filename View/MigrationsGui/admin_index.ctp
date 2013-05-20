<h2><?php echo __('Schema Migrations')?></h2>

<?php
$runUrlTpl  = array('action'=>'command', 'run'); 
$resetUrlTpl = array('action'=>'command', 'reset');
$_showAll    = !empty($this->request->params['named']['all']);
?>

<div class="btn-toolbar">
<div class="btn-group">
    <?php echo $this->Html->link(__('Run All'), am($runUrlTpl, array('all')), array('class'=>'btn btn-primary', 'icon'=>'play')); ?>
    <?php
        if ($_showAll) {
            echo $this->Html->link(__('Pending'), array('all'=>false), array('class'=>'btn btn-success', 'icon'=>'list'));
        } else { 
            echo $this->Html->link(__('All'), array('all'=>true), array('class'=>'btn btn-success', 'icon'=>'list'));
        } 
    ?>
    <?php  ?>
</div>
</div>

<?php
if (empty($mappings)) {
    if ($_showAll) {
        echo $this->MigrationsGui->alert(__('There are no migrations.'));
    } else {
        echo $this->MigrationsGui->alert(__('There are no pending migrations.'), array('class'=>' alert-success'));
    }
    
    return;
} 
?>

<table class="table">
<thead>
<tr>
    <th><?php echo __('No.')?></th>
    <th><?php echo __('Name'); ?></th>
    <th><?php echo __('Version'); ?></th>
    <th><?php echo __('Applied'); ?></th>
</tr>
</thead>
<?php foreach ($mappings as $plugin=>$pluginMapping) : ?>

<?php
$prepend = array('-p', Inflector::humanize($plugin));

$runUrl  = array_merge($runUrlTpl, $prepend);
$upUrl   = array_merge($runUrl, array('up')); 
$downUrl = array_merge($runUrl, array('down')); 
$runAllUrl = array_merge($runUrl, array('all')); 
$resetUrl = array_merge($resetUrlTpl, $prepend);
?>

<tbody>
    <tr>
        <th colspan="4">
            <?php echo Inflector::humanize($plugin); ?>
            <div class="btn-group">
                <?php echo $this->Html->link(__('All'), $runAllUrl, array('title'=>__('Migrate All'), 'class'=>'btn btn-mini', 'icon'=>'play'))?>
                <?php echo $this->Html->link(__('Down'), $downUrl, array('title'=>__('Migrate Down'), 'class'=>'btn btn-mini', 'icon'=>'step-backward'))?>
                <?php echo $this->Html->link(__('Up'), $upUrl, array('title'=>__('Migrate Up'), 'class'=>'btn btn-mini', 'icon'=>'step-forward'))?>
                <?php echo $this->Html->link(__('Reset'), $resetUrl, array('title'=>__('Reset'), 'class'=>'btn btn-mini', 'icon'=>'remove'))?>
            </div>
        </th>
    </tr>
    <?php echo $this->end(); ?>
    <?php $i = count($pluginMapping); $_isCurrent = null; ?>
    <?php foreach ($pluginMapping as $j=>$m) : ?>
    <?php
        $_isApplied = !empty($m['migrated']);
        
        if ($_isApplied) {
            if (!isset($_isCurrent)) {
                $_isCurrent = TRUE;
            } else {
                $_isCurrent = FALSE;
            }
        }
        
        if ($_isCurrent) {
            $trClass = 'current';
        } elseif ($_isApplied) {
            $trClass = 'applied';
        } else {
            $trClass = 'pending';
        }
        
        $applyVersionUrl = array_merge($runUrl, array($m['version']));
    ?>
    <tr class="<?php echo $trClass?>">
        <td>
            <?php echo $i--; ?>.
            <?php if ($_isApplied) {
                echo $this->MigrationsGui->icon('ok');
            }?>
        </td>
        <td>
            <?php
                $title = Inflector::humanize(Inflector::underscore($m['class']));
                if ($applyVersionUrl) { 
                    $title = $this->Html->link($title, $applyVersionUrl); 
                }
                echo $title;
            ?>
            [<?php echo $this->Html->link($_isApplied ? __('clear') : __('mark ok'), array('action'=>'clear', $m['version'], $plugin, !$_isApplied)) ?>]
        </td>
        <td>
            <?php if ($m['version'] > 100000000) {
                echo $this->Time->niceShort($m['version']);
            } else {
                echo $m['version'];
            } ?>
        </td>
        <td>
            <?php if ($_isApplied) {
                echo $this->Time->niceShort($m['migrated']);
            } ?>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
<?php endforeach; ?>
</table>

<?php $this->Html->css('/migrations/css/styles', null, array('block'=>'css'));?>

