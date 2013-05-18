<?php
class MigrationsGuiHelper extends AppHelper
{
    public $helpers = array('Html');
    
    public function icon($icon)
    {
        if (method_exists($this->Html, 'icon')) {
            return $this->Html->icon($icon);
        }
        
        return $this->Html->tag('i', $icon, array('class'=>"icon-{$icon}"));
    }
    
    public function alert($message, $options = array())
    {
        if (!is_array($message)) {
            $message = compact('message');
        }
        $options = $message + $options;
        
        $element = 'alert';
        
        if (method_exists($this->Html, 'icon')) {
            $element = 'TwitterBootstrap.alert';
        }
        
        return $this->_View->element($element, $options);
    }
}