# Define custom paths
set :deploy_config_path, File.expand_path('build/capistrano/deploy.rb')
set :stage_config_path, 'build/capistrano/stages'

# Default configuration
require 'capistrano/setup'
require 'capistrano/deploy'

# Additional plugins
require 'capistrano/composer'
require 'capistrano/symfony'

# Additional tasks
Dir.glob('build/capistrano/lib/capistrano/tasks/*.rake').each { |r| import r }

