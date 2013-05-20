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
    
    public function admin_index($type = null)
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
        $showAll  = !empty($this->request->params['named']['all']);
        
        foreach ($types as $type) {
            $type = Inflector::underscore($type);
            try {
                $mapping = $Version->getMapping($type);
                
                if (!$showAll) {
                    // Remove already applied migrations
                    foreach ((array)$mapping as $i=>$m) {
                        if (!empty($m['migrated'])) {
                            unset($mapping[$i]);
                        } 
                    }
                }
                
                if (empty($mapping)) {
                    // No migrations
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
    
    public function admin_command($command)
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
                @$Shell->runCommand($command, $cparams);
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
    
    public function admin_clear($version, $type, $migrated = false)
    {
        $Shell = $this->_getShell();
        $Shell->Version->setVersion($version, $type, $migrated);
        
        $this->redirect($this->referer());
    }
    
    protected function _getShell()
    {
        static $Shell = NULL;
        
        if (!isset($Shell)) {
            $Shell = $this->_createShell();
            $Shell->startup();
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
        
        $Shell->params = array(
            'no-auto-init' => false,
            'precheck'     => true,
        );
        
        return $Shell;
    }
}
