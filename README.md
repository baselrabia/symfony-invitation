# Symfony Invitation
REST API based invitation system that allows the following actions:
1. One user aka the Sender can send an invitation to another user aka the
Invited.
2. The Sender can cancel a sent invitation.
3. The Invited can either accept or decline an invitation.


## # Using
- PHP 7 (ideally 7.4) 
- Symfony Framework (version 4.4)
- MySQL Database
- PHPUnit Testing Framework
- sqlite In-memory DB for Testing

## # Setup
- `git clone https://github.com/baselrabia/symfony-invitation.git`
- `cd symfony-invitation`
- `composer install`
- `php -S localhost:9000 -t public/`


## # Testing
- tests are running In-memory
so you need to install sqlite extension to be able to test the testcases 
- run `sudo apt-get install php7.4-sqlite`
- ` php bin/phpunit`
### why use In-memory DB for testing ? 
Testing code that interacts with a database can be a massive pain. Some developers mock database abstractions, and thus do not test the actual query. Others create a test database for the development environment, but this can also be a pain when it comes to continuous integration and maintaining the state of this database
In-memory databases are an alternative to these options. As they exist only in the memory of the application, they are truly disposable and great for testing.
## API Collection
### Link: [Symfony Invitation Postman API Collection](https://documenter.getpostman.com/view/21704805/2s83maLQwy)
- using header field "login_by" to auth as a user, it's workaround as authentication is not our focus for now.

![Screenshot from 2022-09-29 12-04-45](https://user-images.githubusercontent.com/27627958/193003589-2692f2ac-158b-491e-92a9-fafa52ca0beb.png)
