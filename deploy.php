<?php
namespace Deployer;

require 'recipe/symfony4.php';

/*
 * Run either 'deploy' (Symfony 4 apps) or 'mydeploy' (adjusted for shared host one.com).
 * If running deployer as a project dependency on Windows you may need to run this:
 * php vendor/deployer/deployer/bin/dep deploy
 * instead of php vendor/bin/dep deploy
 */

// Project name
set('application', 'homepage');

// Project repository
set('repository', 'https://github.com/andersbjorkland/project-online');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);
set('ssh_multiplexing', false);

set('composer_options', '{{composer_action}} --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');


// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts
host('anders')
    ->set('deploy_path', '~/{{application}}')
    ->set('http_user', 'andersbjorkland.online')
;
    
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

/* Uploads built assets from local to remote. Requires rsync.
 * Useful when you use Symfony encore/webpack and remote machine doesn't support npm/yarn.
 */
task('upload:build', function() {
    upload("public/build/", '{{release_path}}/public/build/');
});

task('upload:build', function() {
    upload("public/build/", '{{release_path}}/public/build/');
});

task('init:database', function() {
    run('{{bin/php}} {{bin/console}} doctrine:schema:create');
});

task('echo:options', function() {
    writeln('OPTIONS: {{composer_options}}');
});

task('build', function () {
    run('cd {{release_path}} && build');
});

task('initialize', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:unlock',
    'cleanup',
]);

task('mydeploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:symlink',
    'copy:public',
    'deploy:unlock',
    'cleanup',
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
//after('deploy:unlock', 'copy:public');


// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');