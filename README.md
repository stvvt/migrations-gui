CakePHP Migrations GUI Plugin
==============

A CakePHP 2.x plugin that makes it posible to review and apply schema migrations via web interface.

## Intro

Schema migration is always an important part of application development process. A great tool for automating this
process is [CakeDC's Migration Plugin](https://github.com/CakeDC/migrations). Check its page if you need more 
information about schema migrations or how to use Migration Plugin from command line. However, a number of hosting 
plans don't allow SSH nor any other access to command line. In this case you could use this plugin to review and apply 
yours or teammates' generated schema migrations.

## Requirements

 * CakePHP 2.x
 * [CakeDC's Migration Plugin](https://github.com/CakeDC/migrations) installed

## Installing

Install the plugin as usual, using one of well known methods for installing any CakePHP plugin

 * download plugin source and extract it in your app's `Plugins` directory ...
 * or clone the code
  
   ```
   $ git clone git://github.com/stvvt/migrations-gui.git Plugins/MigrationsGui
   ```
 * or use it as a git submodule

   ```
   $ git submodule add git://github.com/stvvt/migrations-gui.git Plugin/MigrationsGui
   
Finally, enable the plugin by adding the following line in `app/config/bootstrap.php`:

```
CakePlugin::load('Migrations');
```

Don't forget that this plugin is simply a front end, thе tough work is still carried out by 
[CakeDC's Migration Plugin](https://github.com/CakeDC/migrations), so it MUST be installed prior to use.

## Usage

Navigate your browser to ```http://example.org/path/to/yourapp/migrations_gui``` and enjoy.

## Disclaimer

Please note, that this code is in very early stage of development and probably contains bugs and problems.
Issues and/or pull requests are wellcome.
