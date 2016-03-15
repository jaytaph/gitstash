module Capistrano
  class FileNotFound < StandardError
  end
end

# Run install composer.phar in shared, and make sure we use `composer.phar` for composer commands
namespace :deploy do
   before :starting, :map_composer_command do
        on roles(:app) do |server|
            SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"
        end
    end

    after :starting, 'composer:install_executable'
end

