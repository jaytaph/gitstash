# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'gitstash'

# Use git for publishing
set :scm, :git
set :repo_url, 'git@github.com:jaytaph/gitstash.git'

# Deploy_to directory is overridden by the different stages
set :deploy_to, ""

# Set logging
set :format, :pretty
set :log_level, :info

# Files that are used between releases
set :linked_files, [ 'app/config/parameters.yml' ]
set :linked_dirs, [ 'app/logs' ]

# How many release to keep
set :keep_releases, 5

set :symfony_directory_structure, 2
set :sensio_distribution_version, 4

set :use_set_permissions, true
set :permission_method, :acl
set :file_permissions_users, [ "nginx" ]
set :file_permissions_paths, [ fetch(:log_path), fetch(:cache_path) ]


before 'symfony:cache:warmup', 'symfony:migrations:migrate'

namespace :deploy do
  after :finishing, 'deploy:cleanup'
end
