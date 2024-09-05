pipeline {
    agent {
        docker {
            image 'a5402f11b1e894c87e7e665c2359176a8e0f042a982b33b23da6c7d51966a99c' // Remplace par l'image Docker appropriée
        }
    }
    
    stages {
        stage('Checkout') {
            steps {
                git (
                    url: 'https://github.com/hugomaume/symfony3wa/',
                    branch: 'master'
                )
            }
        }
        
       /* stage('Install dependencies') {
            steps {
                sh 'composer install'
            }
        }*/
        
        stage('Run tests') {
            steps {
                sh 'php bin/phpunit --filter'
            }
        }
    }
}
