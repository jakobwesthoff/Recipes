#!/usr/bin/env php
<?php
namespace Recipes;

require __DIR__ . '/../main/Recipes/bootstrap.php';

$dic = new DIC\Base();
$dic->environment = 'development';


$userGateway = $dic->userGateway;

if ($argc < 1)
{
    echo <<<EOF
Usage {$argv[0]} <user> [<user>, ...]

Add the given usernames to the database.

EOF;

    exit(1);
}

for($i=1; $i < $argc; ++$i)
{
    $data = array();
    $data['login'] = $argv[$i];

    echo "About to create user: ", $data['login'], "\n";

    $data['name']  = interactiveAsk( "Real Name: " );
    $data['email'] = interactiveAsk( "EMail Address: " );

    $password = null;
    while ( $password === null )
    {
        $password = interactiveAsk( "Password: ", null, true );
    }
    $data['auth_infos'] = getPasswordHash( $password );


    $userId = $userGateway->createUser( $data['login'] );
    $userGateway->updateUserData( $userId, $data );
    echo "User {$data['login']} created.\n\n";
}

/**
 * Return a hashed version of the given password to be used for later auth
 * checking
 *
 * @param $password
 * @return array
 */
function getPasswordHash( $password )
{
    return array(
        md5( 'arbit_' . $password ),
        sha1( 'arbit_' . $password ),
    );
}

/**
 * Interactively ask the user for something and return the result
 *
 * @param string $prompt
 * @param string|null $default
 * @param bool $silent
 * @return string|null
 */
function interactiveAsk( $prompt = "", $default = null, $silent = false )
{
    echo $prompt;

    if ( $silent === true )
    {
        system('stty -echo');
    }

    $result = trim( fgets( STDIN ) );

    if ( $silent === true )
    {
        system('stty echo');
        // Newline has been eaten up by the silence, therefore echo it manually
        echo "\n";
    }

    if ( $result === "" )
    {
        return $default;
    }

    return $result;
}
