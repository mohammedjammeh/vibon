<?php
namespace Deployer;

require 'recipe/laravel.php';

set('application', 'vibon');
set('repository', 'ssh://git@bitbucket.org/itsyourboymo/vibon.git');
set('default_stage', 'production');
set('git_tty', false);


// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts
host('vibon.co.uk')
    ->stage('production')
    ->identityFile('~/.ssh/id_server')
    ->set('deploy_path', '/var/www/html');
    
// Tasks
task('pwd', function () {
    $result = run('pwd');
    writeln("Current dir: $result");
});











task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');


set('allow_anonymous_stats', true);
