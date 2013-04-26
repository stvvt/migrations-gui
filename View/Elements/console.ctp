<h2><?php echo $message; ?></h2>
<?php if (trim($stdout)) : ?>
<h3>Stdout</h3>
<pre>
<?php echo $stdout; ?>
</pre>
<?php endif; ?>

<?php if (trim($stderr)) : ?>
<h3>Stderr</h3>
<pre>
<?php echo $stderr; ?>
</pre>
<?php endif; ?>
