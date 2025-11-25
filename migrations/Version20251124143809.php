<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124143809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, categoria_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, INDEX IDX_4E10122D3397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE compra (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, proveedor_id INT NOT NULL, fecha DATETIME NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_9EC131FFDB38439E (usuario_id), INDEX IDX_9EC131FFCB305D73 (proveedor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE detalle_compra (id INT AUTO_INCREMENT NOT NULL, compra_id INT NOT NULL, producto_id INT NOT NULL, categoria_id INT NOT NULL, cantidad INT NOT NULL, precio_unitario DOUBLE PRECISION NOT NULL, subtotal INT NOT NULL, INDEX IDX_F219D258F2E704D7 (compra_id), INDEX IDX_F219D2587645698E (producto_id), INDEX IDX_F219D2583397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE detalle_venta (id INT AUTO_INCREMENT NOT NULL, producto_id INT NOT NULL, venta_id INT NOT NULL, cantidad INT NOT NULL, precio_unitario DOUBLE PRECISION NOT NULL, subtotal DOUBLE PRECISION NOT NULL, INDEX IDX_5191A4017645698E (producto_id), INDEX IDX_5191A401F2A5805D (venta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE inventario (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE producto (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion LONGTEXT DEFAULT NULL, precio DOUBLE PRECISION DEFAULT NULL, precio_venta DOUBLE PRECISION NOT NULL, stock INT NOT NULL, INDEX IDX_A7BB06153397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE proveedor (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, direccion VARCHAR(255) DEFAULT NULL, telefono VARCHAR(100) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, apellido_paterno VARCHAR(255) DEFAULT NULL, apellido_materno VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE venta (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, fecha DATETIME NOT NULL, total DOUBLE PRECISION NOT NULL, estado VARCHAR(20) NOT NULL, INDEX IDX_8FE7EE55DB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categoria ADD CONSTRAINT FK_4E10122D3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compra ADD CONSTRAINT FK_9EC131FFDB38439E FOREIGN KEY (usuario_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compra ADD CONSTRAINT FK_9EC131FFCB305D73 FOREIGN KEY (proveedor_id) REFERENCES proveedor (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_compra ADD CONSTRAINT FK_F219D258F2E704D7 FOREIGN KEY (compra_id) REFERENCES compra (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_compra ADD CONSTRAINT FK_F219D2587645698E FOREIGN KEY (producto_id) REFERENCES producto (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_compra ADD CONSTRAINT FK_F219D2583397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_venta ADD CONSTRAINT FK_5191A4017645698E FOREIGN KEY (producto_id) REFERENCES producto (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_venta ADD CONSTRAINT FK_5191A401F2A5805D FOREIGN KEY (venta_id) REFERENCES venta (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto ADD CONSTRAINT FK_A7BB06153397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE venta ADD CONSTRAINT FK_8FE7EE55DB38439E FOREIGN KEY (usuario_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE categoria DROP FOREIGN KEY FK_4E10122D3397707A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compra DROP FOREIGN KEY FK_9EC131FFDB38439E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE compra DROP FOREIGN KEY FK_9EC131FFCB305D73
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_compra DROP FOREIGN KEY FK_F219D258F2E704D7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_compra DROP FOREIGN KEY FK_F219D2587645698E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_compra DROP FOREIGN KEY FK_F219D2583397707A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_venta DROP FOREIGN KEY FK_5191A4017645698E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalle_venta DROP FOREIGN KEY FK_5191A401F2A5805D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto DROP FOREIGN KEY FK_A7BB06153397707A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE venta DROP FOREIGN KEY FK_8FE7EE55DB38439E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categoria
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE compra
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE detalle_compra
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE detalle_venta
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE inventario
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE producto
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE proveedor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE venta
        SQL);
    }
}
