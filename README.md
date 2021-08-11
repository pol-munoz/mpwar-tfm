# Kunlabo - A platform for user testing in co-creative scenarios

## Usage

1. Crete a `.env` file in the root directory with the following parameters:
   - MYSQL_DATABASE = "Name of the MySQL database to create/use."
   - MYSQL_USER = "Name of the MySQL user the application will use."
   - MYSQL_USER_PASSWORD = "Password for the MySQL user the application will use."
   - MYSQL_ROOT_PASSWORD = "Password to set up for the MySQL root user."
    
2. Use the `make run` command in the root directory.

## Deployment

Make sure to:
- In regards to the `kunlabo` Symfony project:
    - [Generate production secrets](https://symfony.com/doc/current/configuration/secrets.html) for the `DB_USER` and `DB_PASSWORD` environment variables, which should match the 
    previously defined `MYSQL_USER` and `MYSQL_USER_PASSWORD`, respectively.
      
## Architecture

This project makes use of several Domain-Driven Design practices and other related concepts. Specifically, the project follows the Hexagonal Architecture pattern, and uses Command Query Responsibility Segregation. 

## Frameworks and Libraries

- [Symfony](https://symfony.com/) as the main framework. Including the following components:
    - [Symfony Security](https://symfony.com/doc/current/security.html) to manage user accounts with the [new authenticator system](https://symfony.com/doc/current/security/authenticator_manager.html).
    - [Symfony Messenger](https://symfony.com/doc/current/messenger.html) to implement Command, Query and Domain Event buses.

- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html) as the Object-Relational-Mapper.

- [Ramsey UUID](https://uuid.ramsey.dev/en/stable/) to provide UUID-v4 unique entity identifiers.