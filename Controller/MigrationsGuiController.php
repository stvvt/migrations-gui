<?php
App::uses('MigrationsGuiAppController', 'MigrationsGui.Controller');
App::uses('MigrationGuiShell', 'MigrationsGui.Console/Command');
App::uses('MigrationGuiConsoleOutput', 'MigrationsGui.Console');
App::uses('ConsoleInput', 'Console');

/**
 * MigrationsGui Controller
 *
 */
class MigrationsGuiController extends MigrationsGuiAppController
{
    public $components = array('Session');
    
    public function index($type = null)
    {
        if (isset($type)) {
            $types = (array)$type;
        } else {
            $types = CakePlugin::loaded();
            ksort($types);
            array_unshift($types, 'App');
        }
        
        $Shell = $this->_getShell();
        $Shell->runCommand('init', array());
        
        $Version = $Shell->Version;
        
        $errors = array();
        $mappings = array();
        
        foreach ($types as $type) {
            $type = Inflector::underscore($type);
            try {
                $mapping = $Version->getMapping($type);
                
                if (empty($mapping)) {
                    continue;
                }
                
                krsort($mapping);
                
                $mappings[$type] = $mapping;
            } catch (MigrationVersionException $e) {
                $errors[$type] = $e->getMessage();
            }
        }
        
        $this->set(compact('mappings', 'errors'));
    }
    
    public function command($command)
    {
        $params = (array)$this->request->params['pass'];
        $plugin = null;
        
        foreach ($params as $i=>$p) {
            if ($p == '-p') {
                unset($params[$i]);
                if (isset($params[$i+1])) {
                    $plugin = $params[$i+1];
                    unset($params[$i+1]);
                }
                break;
            }
        }
        
        if (isset($plugin)) {
            $plugins = (array)$plugin;
        } else {
            $plugins = CakePlugin::loaded();
            array_unshift($plugins, 'App');
        }
        
        $stdout = $stderr = '';
        
        foreach ($plugins as $plugin) {
            $Shell = $this->_createShell();
        
            if (strcasecmp($plugin, 'app') !== 0) {
                $cparams = array_merge($params, array('-p', $plugin));
            } else {
                $cparams = $params;
            }
            
            try {
                $Shell->runCommand($command, $cparams);
                $stdout .= (string)$Shell->stdout;
                $stderr .= (string)$Shell->stderr;
            } catch (MigrationGuiStopException $e) {
                if ($e->getCode() != 1) {
                    $Shell->err('Exception: ' . $e->getMessage());
                    
                    $stderr .= (string)$Shell->stdout;
                    $stderr .= (string)$Shell->stderr;
                }
            }
        }
        
        $this->Session->setFlash('Migration Shell', 'MigrationsGui.console', compact('stdout', 'stderr'));
        $this->redirect(array('action'=>'index'));
    }
    
    protected function _getShell()
    {
        static $Shell = NULL;
        
        if (!isset($Shell)) {
            $Shell = $this->_createShell();
        }
        
        return $Shell;
    }
    
    protected function _createShell()
    {
        $stdin  = new ConsoleInput('php://memory');
        $stdout = new MigrationGuiConsoleOutput('php://memory');
        $stderr = new MigrationGuiConsoleOutput('php://memory');
        
        $Shell = new MigrationGuiShell($stdout, $stderr, $stdin);
        $Shell->interactive = false;
        
        return $Shell;
    }
}
