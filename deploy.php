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

task('cache:clear', function () {
    run('php {{release_path}}/bin/console cache:clear');
});

/* Is used when symlink from public folder doesn't behave as expected.
 * The downside of using it this way is that it doesn't remove files no longer present in git repo.
 * Assumed public directory is /www
 */
task('copy:public', function() {
    run('cp -R {{release_path}}/public/*  /www && cp -R {{release_path}}/public/.[^.]* /www');
});

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy:unlock', 'copy:public');


// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');

