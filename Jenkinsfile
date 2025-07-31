pipeline {
    agent any

    environment {
        APP_ENV = 'testing'
    }

    stages {

        stage('Fix Permissions') {
            steps {
                sh '''
                    sudo chown -R jenkins:jenkins /var/www/astuparf
                '''
            }
        }


        stage('Mise à jour du code') {
            steps {
                sh '''
                    git config --global --add safe.directory /var/www/astuparf || true
                    cd /var/www/erpfront

                    git reset --hard
                    git clean -fd
                    git pull origin main
                '''
            }
        }


        stage('Composer install') {
            steps {
                sh '''
                    cd /var/www/astuparf
                    composer install --no-interaction --prefer-dist --optimize-autoloader
                '''
            }
        }

        stage('NPM build') {
            steps {
                sh '''
                    cd /var/www/astuparf
                    npm install
                    npm run build
                '''
            }
        }

    }

    post {
        success {
            echo "Build terminé avec succès !"
        }
        failure {
            echo "Échec du pipeline "
        }
    }
}
