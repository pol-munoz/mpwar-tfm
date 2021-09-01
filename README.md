# Kunlabo - A platform for user testing in co-creative scenarios

## Usage

1. Crete a `.env` file in the root directory with the following parameters:
   - MYSQL_DATABASE = "Name of the MySQL database to create/use."
   - MYSQL_USER = "Name of the MySQL user the application will use."
   - MYSQL_PASSWORD = "Password for the MySQL user the application will use."
   - MYSQL_ROOT_PASSWORD = "Password to set up for the MySQL root user."
   - JWT_SECRET_KEY = "256-bit key used to generate the Mercure JWTs."
    
2. To set up the project for the first time, use the `make run` command in the root directory. Docker needs to be installed and running.
    - From this point onwards, using the `make start` command in the root directory is enough.

## Architecture

This project makes use of several Domain-Driven Design practices and other related concepts. Specifically, the project follows the Hexagonal Architecture pattern, and uses Command Query Responsibility Segregation. 

## Frameworks and Libraries

### Frontend:

- [Stimulus](https://stimulus.hotwired.dev/) as a light JavaScript framework.

- [Turbo](https://turbo.hotwired.dev/) to improve perceived performance by performing requests in the background and updating instead of reloading the page.
                       
- [Chart.js](https://www.chartjs.org/) to display charts.

- [FontAwesome](https://fontawesome.com/) for icons.

- [Webpack Encore](https://symfony.com/doc/current/frontend.html) to manage, process and bundle assets.

### Backend:

- [Mercure](https://mercure.rocks/) for realtime server->client communication.

- [Monolog](https://seldaek.github.io/monolog/) to write study logs to a file (in bulk).

- [Elastic Stack](https://www.elastic.co/elastic-stack/) to efficiently structure and index logs from said file.

- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html) as an Object-Relational-Mapper.

- [Twig](https://twig.symfony.com/) to define and render templates from the backend.

- [Symfony](https://symfony.com/) as the main framework. Including the following components:
    - [Symfony Security](https://symfony.com/doc/current/security.html) to manage user accounts with the [new authenticator system](https://symfony.com/doc/current/security/authenticator_manager.html).
    - [Symfony Messenger](https://symfony.com/doc/current/messenger.html) to implement Command, Query and Domain Event buses.
    - [Symfony UX Turbo](https://github.com/symfony/ux-turbo) to bridge Turbo and Symfony.
    - [Symfony UX Chart.js](https://github.com/symfony/ux-chartjs) to bridge Chart.js and Symfony.
    - Other bundles for the rest of the relevant technologies (Twig, Mercure, Monolog...)
    
- [Ramsey UUID](https://uuid.ramsey.dev/en/stable/) to provide UUID-v4 unique entity identifiers.
