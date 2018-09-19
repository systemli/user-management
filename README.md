# Systemli User Management

[![Build Status](https://travis-ci.org/systemli/user-management.svg?branch=master)](https://travis-ci.org/systemli/user-management)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/systemli/user-management/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/systemli/user-management/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/systemli/user-management/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/systemli/user-management/?branch=master)

## Installation

Requirements: Vagrant (VirtualBox)

    git submodule update --init
    cd vagrant && vagrant up && vagrant ssh

    cd /vagrant
    
    # create database and schema
    bin/console doctrine:schema:create --env dev
    
    # load sample data
    bin/console doctrine:fixtures:load

The `doctrine:fixtures:load` command will create four new accounts with
corresponding roles (`admin`, `user`, `support`, `suspicious`) as well
as some random aliases and vouchers. The domain for all accounts is
"example.org" and the password is "password".

If you want to see more details about how the users are created, see
`src/AppBundle/DataFixtures`.

## Tests

    cd vagrant && vagrant up && vagrant ssh
    cd /vagrant
    bin/phpunit
    bin/behat -f progress
    das ist ein test

## Update assets

    cd vagrant && vagrant up && vagrant ssh
    cd /vagrant
    yarn  # if you don't already have node_modules
    yarn encore dev

## Commands

This app brings custom commands:

    usrmgmt:munin:account          # Return number of account to munin
    usrmgmt:munin:voucher          # Return number of vouchers to munin
    usrmgmt:registration:mail      # Send registration mail to user
    usrmgmt:report:weekly          # Send weekly report about registrations
    usrmgmt:users:remove           # Remove disabled users maildirs
    usrmgmt:voucher:create         # Create multiple vouchers for user, -c configures amount
    usrmgmt:voucher:unlink         # Unlink redeemed vouchers from users
    
Get more information about each command by running:

    bin/console {{ command }} --help

## Customizations

You can override translation strings individually by putting them into
override localization files at `translations/<lang>/messages.<lang>.yml`.
