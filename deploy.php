<?php
namespace Deployer;

require 'recipe/common.php';

// Configuration
set('repository', 'git@github.com:yannickl88/heimdall-server.git');
set('shared_files', []);
set('shared_dirs', []);
set('writable_dirs', []);
set('ssh_type', 'native');
set('ssh_multiplexing', false);
set('env_vars', function () {
    return 'APP_PUBLISH_DIR="/var/www/heimdall.yannickl88.nl/shared" APP_ENV=prod APP_SECRET=795f66634a9ed0020999906418d8de7e ';
});

// Servers
server('production', 'yannickl88.nl')
    ->user('deploy')
    ->identityFile(__DIR__ . '/keys/deployment.pub', __DIR__ . '/keys/deployment')
    ->set('deploy_path', '/var/www/heimdall.yannickl88.nl');

// Tasks
desc('Restart Apache2 service');
task('php-apache:restart', function () {
    run('sudo apache2ctl graceful');
});
task('deploy:cache:warmup', function () {
    run('{{env_vars}} php {{release_path}}/bin/console cache:clear --env=prod');
});

after('deploy:symlink', 'php-apache:restart');

desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:cache:warmup',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);
