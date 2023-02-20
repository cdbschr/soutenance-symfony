<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305135758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, tel VARCHAR(10) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FCEC9EF79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne_trajet (personne_id INT NOT NULL, trajet_id INT NOT NULL, INDEX IDX_1F219161A21BD112 (personne_id), INDEX IDX_1F219161D12A823 (trajet_id), PRIMARY KEY(personne_id, trajet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, conducteur_id INT NOT NULL, ville_depart_id INT NOT NULL, ville_arrivee_id INT NOT NULL, voiture_id INT NOT NULL, kms INT NOT NULL, date DATETIME NOT NULL, heure_depart TIME NOT NULL, heure_arrivee TIME NOT NULL, place_dispos INT NOT NULL, INDEX IDX_2B5BA98CF16F4AC6 (conducteur_id), INDEX IDX_2B5BA98C497832A6 (ville_depart_id), INDEX IDX_2B5BA98C34AC9A4B (ville_arrivee_id), INDEX IDX_2B5BA98C181A8BA (voiture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(180) NOT NULL, token_api VARCHAR(180) DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B36C6E55B5 (nom), UNIQUE INDEX UNIQ_1D1C63B3742D6553 (token_api), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voiture (id INT AUTO_INCREMENT NOT NULL, conducteur_id INT NOT NULL, marque_id INT NOT NULL, immatriculation VARCHAR(255) NOT NULL, couleur VARCHAR(255) NOT NULL, places INT NOT NULL, INDEX IDX_E9E2810FF16F4AC6 (conducteur_id), INDEX IDX_E9E2810F4827B9B2 (marque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EF79F37AE5 FOREIGN KEY (id_user_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE personne_trajet ADD CONSTRAINT FK_1F219161A21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personne_trajet ADD CONSTRAINT FK_1F219161D12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CF16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES personne (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C497832A6 FOREIGN KEY (ville_depart_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C34AC9A4B FOREIGN KEY (ville_arrivee_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FF16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES personne (id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C42079C3423909');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C420794067ACA7');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C42079C3C6F69F');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C420797B693E7C');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD17679F37AE5');
        $this->addSql('ALTER TABLE person_route DROP FOREIGN KEY FK_AEF23FBD217BBB47');
        $this->addSql('ALTER TABLE person_route DROP FOREIGN KEY FK_AEF23FBD34ECB4E6');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69DF16F4AC6');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D404E8A91');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE person_route');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE brand_car');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE route (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, start_city_id INT NOT NULL, arrival_city_id INT NOT NULL, car_id INT NOT NULL, kms INT NOT NULL, date DATETIME NOT NULL, start_hour TIME NOT NULL, arrival_hour TIME NOT NULL, available_places INT NOT NULL, INDEX IDX_2C42079C3C6F69F (car_id), INDEX IDX_2C42079C3423909 (driver_id), INDEX IDX_2C420797B693E7C (start_city_id), INDEX IDX_2C420794067ACA7 (arrival_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, surname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, mail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_34DCD17679F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, postal_code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', token VARCHAR(180) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D6495F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE person_route (person_id INT NOT NULL, route_id INT NOT NULL, INDEX IDX_AEF23FBD34ECB4E6 (route_id), INDEX IDX_AEF23FBD217BBB47 (person_id), PRIMARY KEY(person_id, route_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, brand_car_id INT NOT NULL, conducteur_id INT NOT NULL, registration_car VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, color VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, places SMALLINT DEFAULT NULL, model VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_773DE69DF16F4AC6 (conducteur_id), INDEX IDX_773DE69D404E8A91 (brand_car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE brand_car (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079C3423909 FOREIGN KEY (driver_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C420794067ACA7 FOREIGN KEY (arrival_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C420797B693E7C FOREIGN KEY (start_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17679F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE person_route ADD CONSTRAINT FK_AEF23FBD217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_route ADD CONSTRAINT FK_AEF23FBD34ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69DF16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D404E8A91 FOREIGN KEY (brand_car_id) REFERENCES brand_car (id)');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EF79F37AE5');
        $this->addSql('ALTER TABLE personne_trajet DROP FOREIGN KEY FK_1F219161A21BD112');
        $this->addSql('ALTER TABLE personne_trajet DROP FOREIGN KEY FK_1F219161D12A823');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CF16F4AC6');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C497832A6');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C34AC9A4B');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C181A8BA');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FF16F4AC6');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810F4827B9B2');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE personne');
        $this->addSql('DROP TABLE personne_trajet');
        $this->addSql('DROP TABLE trajet');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE ville');
        $this->addSql('DROP TABLE voiture');
    }
}
