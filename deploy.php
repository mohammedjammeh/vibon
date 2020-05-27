<?php
namespace Deployer;

require 'recipe/laravel.php';

set('application', 'vibon');
set('repository', 'ssh://git@bitbucket.org/itsyourboymo/vibon.git');
set('default_stage', 'production');
set('git_tty', false);
set('allow_anonymous_stats', false);

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

// Before && After
before('deploy:symlink', 'artisan:migrate');
after('deploy:failed', 'deploy:unlock');
after('deploy:symlink', 'php-fpm:restart');


task('php-fpm:restart', function () {
    run('sudo service php7.2-fpm restart');
});


