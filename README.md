# Native Cron Bundle

This package provides a Symfony Bundle for the [`mintware-de/native-cron`](https://github.com/mintware-de/native-cron) package.

The main purpose is easily managing cron jobs for your application. 

## Installation
```bash
composer require mintware-de/native-cron-bundle
```

Since cron jobs stored differently based on the operating system, you've to set the `CrontabFileLocatorInterface` 
in your service definition.

This bundle will only work with drop-in crontab files. So make sure that the implementation supports that feature.
```yaml
services:
    MintwareDe\NativeCron\Filesystem\CrontabFileLocatorInterface:
        class: MintwareDe\NativeCron\Filesystem\DebianCrontabFileLocator
```

## Requirements
- PHP 8.1+
- Symfony 6.1

## Usage


### Mark Commands as Cronjobs
After installing the bundle you can use the `#[CronJob(...)]` annotation (`MintwareDe\NativeCronBundle\Attribute\CronJob`)
on your console commands.

This annotation accepts 2-5 arguments:
```php

#[CronJob(
    name: 'my_cron_job',    // This is the name of the cron job. It is used to trigger the command
                            // using the mw:cron:run command
                      
    #           .---------------- minute (0 - 59)
    #           | .-------------- hour (0 - 23)
    #           | | .------------ day of month (1 - 31)
    #           | | | .---------- month (1 - 12)
    #           | | | | .-------- day of week (0 - 6; Sunday=0)
    #           | | | | |   
    executeAt: '* * * * *', // This is the execution time definition. The format is identical to the crontab file. 
    user: 'root',           // The user that will be used to run the command. Optional, default = root 
    arguments: [            // The arguments that are passed to your command. See also here (ArrayInput): https://symfony.com/doc/current/console/calling_commands.html 
        'name'    => 'Foo', // InputArgument
        '--yell'  => true,  // Input option
    ],
)]
#[AsCommand(...)]
class MyCommand extends \Symfony\Component\Console\Command\Command {
    // ...
}
```

### List / Install / Uninstall
You can use the Symfony console to list, install and uninstall the cron jobs.
**Remember: Install and uninstall may require higher user privileges!** (su root / sudo etc.)

#### List
```bash
$ php bin/console mw:cron:list

Cron jobs
=========

 -------------- ------------ ---------------- ------------ 
  Name           Execute At   Arguments        Command     
 -------------- ------------ ---------------- ------------ 
  my_cron_job    * * * * *    []               MyCronJob   
  my_cron_job2   0 1 * * *    {"arg2":"foo"}   MyCronJob2  
 -------------- ------------ ---------------- ------------ 
```

#### Install
```bash
$ php bin/console mw:cron:install

New crontab content
===================

* * * * * root /usr/local/Cellar/php/8.1.10_1/bin/php /source/bin/console mw:cron:run my_cron_job
0 1 * * * root /usr/local/Cellar/php/8.1.10_1/bin/php /source/bin/console mw:cron:run my_cron_job2

 Confirm crontab? (yes/no) [yes]:
 > yes


```

#### Uninstall
```bash
$ php bin/console mw:cron:uninstall

Uninstall cron jobs
===================

                                                                                                                        
 [WARNING] The following cron jobs will be uninstalled:                                                                 
                                                                                                                        

* * * * * root /usr/local/Cellar/php/8.1.10_1/bin/php /source/bin/console mw:cron:run my_cron_job
0 1 * * * root /usr/local/Cellar/php/8.1.10_1/bin/php /source/bin/console mw:cron:run my_cron_job2

 Confirm uninstall? (yes/no) [yes]:
 > 



```
