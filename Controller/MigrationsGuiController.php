<?php
App::uses('MigrationsGuiAppController', 'MigrationsGui.Controller');
App::uses('MigrationShell', 'Migrations.Console/Command');
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
        $Shell = $this->_getShell();
        
        $Shell->runCommand($command, (array)$this->request->params['pass']);
        
        $this->set('stdout', (string)$Shell->stdout);
        $this->set('stderr', (string)$Shell->stderr);
        
        $this->Session->setFlash('Migration Shell', 'MigrationsGui.console', array('stdout'=>(string)$Shell->stdout, 'stderr'=>(string)$Shell->stderr));
        $this->redirect(array('action'=>'index'));
    }
    
    protected function _getShell()
    {
        static $Shell = NULL;
        
        if (!isset($Shell)) {
            $stdin  = new ConsoleInput('php://memory');
            $stdout = new MigrationGuiConsoleOutput('php://memory');
            $stderr = new MigrationGuiConsoleOutput('php://memory');
            
            $Shell = new MigrationShell($stdout, $stderr, $stdin);
            $Shell->interactive = false;
        }
        
        return $Shell;
    }
}
