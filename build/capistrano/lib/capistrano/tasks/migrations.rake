module Capistrano
    class FileNotFound < StandardError
    end
end

namespace :symfony do
    namespace :migrations do
        task :migrate do
            on roles(:db) do
                invoke 'symfony:console', 'doctrine:migrations:migrate', '--no-interaction'
            end
        end
    end
end
