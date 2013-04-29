<?php
App::uses('MigrationShell', 'Migrations.Console/Command');
App::uses('MigrationGuiStopException', 'MigrationsGui.Lib');

/**
 * Migration GUI shell.
 *
 * @package       migrations_gui
 */
class MigrationGuiShell extends MigrationShell
{
    protected function _stop($status = 0)
    {
        throw new MigrationGuiStopException('_stop', $status);
    }
}
