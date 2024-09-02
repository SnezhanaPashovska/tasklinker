<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902130534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE employee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE time_entry_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE employee (id INT NOT NULL, time_entry_id INT NOT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(500) NOT NULL, role SMALLINT NOT NULL, contract VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, arrival_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5D9F75A11EB30A8E ON employee (time_entry_id)');
        $this->addSql('COMMENT ON COLUMN employee.arrival_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE employee_project (employee_id INT NOT NULL, project_id INT NOT NULL, PRIMARY KEY(employee_id, project_id))');
        $this->addSql('CREATE INDEX IDX_AFFF86E18C03F15C ON employee_project (employee_id)');
        $this->addSql('CREATE INDEX IDX_AFFF86E1166D1F9C ON employee_project (project_id)');
        $this->addSql('CREATE TABLE project (id INT NOT NULL, tags_id INT DEFAULT NULL, status_id INT NOT NULL, name VARCHAR(255) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deadline TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE8D7B4FB4 ON project (tags_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE6BF700BD ON project (status_id)');
        $this->addSql('COMMENT ON COLUMN project.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN project.deadline IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE status (id INT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag_task (tag_id INT NOT NULL, task_id INT NOT NULL, PRIMARY KEY(tag_id, task_id))');
        $this->addSql('CREATE INDEX IDX_BC716493BAD26311 ON tag_task (tag_id)');
        $this->addSql('CREATE INDEX IDX_BC7164938DB60186 ON tag_task (task_id)');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, projects_id INT NOT NULL, employees_id INT NOT NULL, status_id INT NOT NULL, time_entry_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, deadline TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB251EDE0F55 ON task (projects_id)');
        $this->addSql('CREATE INDEX IDX_527EDB258520A30B ON task (employees_id)');
        $this->addSql('CREATE INDEX IDX_527EDB256BF700BD ON task (status_id)');
        $this->addSql('CREATE INDEX IDX_527EDB251EB30A8E ON task (time_entry_id)');
        $this->addSql('COMMENT ON COLUMN task.deadline IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE time_entry (id INT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN time_entry.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN time_entry.end_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A11EB30A8E FOREIGN KEY (time_entry_id) REFERENCES time_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee_project ADD CONSTRAINT FK_AFFF86E18C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee_project ADD CONSTRAINT FK_AFFF86E1166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE6BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_task ADD CONSTRAINT FK_BC716493BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_task ADD CONSTRAINT FK_BC7164938DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB251EDE0F55 FOREIGN KEY (projects_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258520A30B FOREIGN KEY (employees_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB256BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB251EB30A8E FOREIGN KEY (time_entry_id) REFERENCES time_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE employee_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE time_entry_id_seq CASCADE');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A11EB30A8E');
        $this->addSql('ALTER TABLE employee_project DROP CONSTRAINT FK_AFFF86E18C03F15C');
        $this->addSql('ALTER TABLE employee_project DROP CONSTRAINT FK_AFFF86E1166D1F9C');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE8D7B4FB4');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE6BF700BD');
        $this->addSql('ALTER TABLE tag_task DROP CONSTRAINT FK_BC716493BAD26311');
        $this->addSql('ALTER TABLE tag_task DROP CONSTRAINT FK_BC7164938DB60186');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB251EDE0F55');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB258520A30B');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB256BF700BD');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB251EB30A8E');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE employee_project');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_task');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE time_entry');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
