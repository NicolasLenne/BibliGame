<?php

namespace App\DataFixtures;

use App\Entity\Console;
use App\Entity\User;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * Permet de TRUNCATE les tables et de remettre les AI à 1
     */
    private function truncate()
    {
        // Désactivation la vérification des contraintes FK
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // On tronque
        $this->connection->executeQuery('TRUNCATE TABLE console');
        $this->connection->executeQuery('TRUNCATE TABLE console_game');
        $this->connection->executeQuery('TRUNCATE TABLE game');
        $this->connection->executeQuery('TRUNCATE TABLE user');
    }
    
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // User part

        $userList = [];

        // User with admin role
        $adminUser = new User();
        $adminUser->setFirstname('Nicolas');
        $adminUser->setLastname('Lenne');
        $adminUser->setEmail('nicolas.lenne@outlook.fr');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setCreatedAt(new DateTime());
        $adminUser->setPassword('$2y$13$GsdDDdWlo/0JtHcmtIgoaeij4BxcS6AMTvAtW4LeiYH6twNXD6N86');
        $userList[] = $adminUser;
        $manager->persist($adminUser);

        // User with user role
        $normalUser = new User();
        $normalUser->setFirstname('Aurélie');
        $normalUser->setLastname('Gralinot');
        $normalUser->setEmail('gralinot.lenne@outlook.fr');
        $normalUser->setRoles(['ROLE_USER']);
        $normalUser->setCreatedAt(new DateTime());
        $normalUser->setPassword('$2y$13$GsdDDdWlo/0JtHcmtIgoaeij4BxcS6AMTvAtW4LeiYH6twNXD6N86');
        $userList[] = $normalUser;
        $manager->persist($normalUser);

        // Console and game part
        $nameConsoles = [
            'Nintendo64', 'Playstation', 'Nintendo Switch', 'Sega Master System', 'Sega Magadrive'
        ];

        $pictureConsoles = [
            'https://upload.wikimedia.org/wikipedia/commons/8/82/Nintendo_64.jpg',
            '',
            'https://static.wikia.nocookie.net/mario/images/7/71/NS_Handheld_Mode.png/revision/latest?cb=20200525112151',
            'https://upload.wikimedia.org/wikipedia/commons/9/98/Master_System_II.jpg',
            ''
        ];

        for ($consoleCount=0; $consoleCount < 5; $consoleCount++) { 
            
            $console = new Console();
            $console->setName($nameConsoles[$consoleCount]);
            $console->setIsLoose(mt_rand(0,1));
            $console->setPhoto($pictureConsoles[$consoleCount]);
            $console->setUser($userList[mt_rand(0,1)]);
            $manager->persist($console);
        }

        $manager->flush();
    }
}
