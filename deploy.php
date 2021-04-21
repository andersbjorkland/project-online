<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'homepage');

// Project repository
set('repository', 'https://github.com/andersbjorkland/project-online');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);
set('ssh_multiplexing', false);

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('satius')
    ->set('deploy_path', '~/{{application}}');    
    
// Tasks
task('symlink:public', function() {
    run('ln -s {{release_path}}/public/*  /www &&  ln -s {{release_path}}/public/.[^.]* /www');
});

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');

