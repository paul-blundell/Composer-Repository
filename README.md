# Composer Repository

A Composer repository build script.
This script uses the BitBucket API to gather a list of GIT repositories before
building your own Composer repository.

## Installation

After cloning the repo

1. Open satis-build.php and edit the BitBucket OAUTH details for your team then set the Name and URL.
1. Run the following commands from your terminal:
    ```
    composer install
    php satis-build.php (output-dir)
    ```
When running the build script you can optionally specify the output directory, the default value is htdocs.

## Using the Repository / SSH Keys

The GIT urls contained in the Satis JSON file are in the format `git@composer:team-name/package-name`, this is using an SSH alias and
so before you can use the repo you will need to add your SSH key and create the alias.

1. Create a new SSH key and save to `~/.ssh/composer`
    ```
    #!bash
    
        ssh-keygen -C 'myemail@domain.com'
    ```
1. Copy your public key `~/.ssh/composer.pub` and go to BitBucket, select your team and click Manage.
1. Click SSH keys on the left and add your key.
1. Open or create the file `~/.ssh/config` and add the following content (be sure to check file paths!):
    ```
    #!bash
    
        Host composer
            HostName bitbucket.org
            IdentityFile ~/.ssh/composer
    ```
