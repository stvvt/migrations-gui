<?php

App::uses('ConsoleOutput', 'Console');

class MigrationGuiConsoleOutput extends ConsoleOutput
{
    public function __construct($stream = 'php://memory')
    {
        parent::__construct($stream);
        
        $this->outputAs(self::RAW);
    }
    
    public function __toString()
    {
        rewind($this->_output);

        return stream_get_contents($this->_output);
    }
}