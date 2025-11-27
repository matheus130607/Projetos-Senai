-- Geração de Modelo físico
-- Sql ANSI 2003 - brModelo.

create database Techfit;
use Techfit;

CREATE TABLE Produtos (
id_produtos int auto_increment not null PRIMARY KEY,
tipo_produtos varchar(100) not null,
nome_produtos varchar(100) not null,
quant_produtos int not null,
data_venc_produtos datetime not null
);

CREATE TABLE Agendamentos (
id_agendamentos int auto_increment not null PRIMARY KEY,
data_agendamentos datetime not null,
id_funcionario int not null,
id_modalidades int not null
);

CREATE TABLE Vende (
id_produtos int not null,
id_funcionario int not  null,
FOREIGN KEY(id_produtos) REFERENCES Produtos (id_produtos)
);

CREATE TABLE Compra (
id_cliente int not null,
id_produtos int not null,
FOREIGN KEY(id_produtos) REFERENCES Produtos (id_produtos)
);

CREATE TABLE Planos (
id_planos int auto_increment not null PRIMARY KEY,
beneficios_planos varchar(255) not null,
nome_planos varchar(100) not null,
id_cliente int not null,
id_funcionario int not  null
);

CREATE TABLE Agenda (
id_agendamentos int not null,
id_cliente int not null,
FOREIGN KEY(id_agendamentos) REFERENCES Agendamentos (id_agendamentos)
);

CREATE TABLE Funcionarios (
id_funcionario int auto_increment not  null PRIMARY KEY,
nome_funcionario varchar(100) not null,
CPF_funcioario varchar(14) not null,
CEP_funcionario char(8) not null,
data_nasc_funcionario datetime not null,
email_funcionario varchar(100) not null,
senha_funcionario varchar(16) not null,
estado_funcionario char(2) default 'SP' not null,
endereco_funcionario varchar(255) not null
);

CREATE TABLE Interage (
id_cliente int not null,
id_funcionario int not  null,
FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario)
);

CREATE TABLE Clientes (
id_cliente int auto_increment not null PRIMARY KEY,
nome_cliente varchar(100) not null,
CPF_cliente varchar(14) not null,
CEP_cliente char(8) not null,
data_nasc_cliente datetime not null,
email_cliente varchar(100) not null,
endereco_cliente varchar(255) not null,
estado_cliente char(2) default 'SP'  not null,
senha_cliente varchar(16) not null
);

CREATE TABLE Modalidades (
nome_modalidades varchar(255) not null,
id_modalidades int auto_increment not null PRIMARY KEY,
tipo_modalidades varchar(100) not null,
id_funcionario int not  null,
FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario)
);

CREATE TABLE Produtos (
    id_produtos int auto_increment not null PRIMARY KEY,
    tipo_produtos varchar(100) not null,
    nome_produtos varchar(100) not null,
    quant_produtos int not null,
    preco_produtos DECIMAL(10, 2) NOT NULL, -- NOVO CAMPO
    data_venc_produtos datetime not null
);

ALTER TABLE Agendamentos ADD FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario);
ALTER TABLE Agendamentos ADD FOREIGN KEY(id_modalidades) REFERENCES Modalidades (id_modalidades);
ALTER TABLE Vende ADD FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario);
ALTER TABLE Compra ADD FOREIGN KEY(id_cliente) REFERENCES Clientes (id_cliente);
ALTER TABLE Planos ADD FOREIGN KEY(id_cliente) REFERENCES Clientes (id_cliente);
ALTER TABLE Planos ADD FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario);
ALTER TABLE Agenda ADD FOREIGN KEY(id_cliente) REFERENCES Clientes (id_cliente);
ALTER TABLE Interage ADD FOREIGN KEY(id_cliente) REFERENCES Clientes (id_cliente);
ALTER TABLE Produtos ADD COLUMN preco_produtos DECIMAL(10, 2) NOT NULL DEFAULT 0.00;

INSERT INTO Funcionarios (nome_funcionario, CPF_funcioario, CEP_funcionario, data_nasc_funcionario, email_funcionario, senha_funcionario, endereco_funcionario) VALUES
('Carlos Andrade', '111.222.333-44', '13480010', '1990-05-15 00:00:00', 'carlos.andrade@email.com', 'senhaForte123', 'Rua das Flores, 10, Centro, Limeira'),
('Beatriz Costa', '222.333.444-55', '13480020', '1992-09-20 00:00:00', 'beatriz.costa@email.com', 'senhaForte124', 'Avenida Saudades, 20, Vila Roxa, Limeira'),
('Fernando Lima', '333.444.555-66', '13482150', '1988-02-10 00:00:00', 'fernando.lima@email.com', 'senhaForte125', 'Rua dos Cravos, 30, Jardim Nobre, Limeira'),
('Daniela Martins', '444.555.666-77', '13484330', '1995-11-30 00:00:00', 'daniela.martins@email.com', 'senhaForte126', 'Avenida Principal, 40, Parque das Nações, Limeira'),
('Ricardo Souza', '555.666.777-88', '13486100', '1993-07-25 00:00:00', 'ricardo.souza@email.com', 'senhaForte127', 'Rua das Orquídeas, 50, Vila Nova, Limeira'),
('Juliana Alves', '666.777.888-99', '13480011', '1998-01-05 00:00:00', 'juliana.alves@email.com', 'senhaForte128', 'Rua das Amoreiras, 60, Centro, Limeira'),
('Lucas Oliveira', '777.888.999-00', '13480021', '1985-03-12 00:00:00', 'lucas.oliveira@email.com', 'senhaForte129', 'Avenida dos Lirios, 70, Vila Roxa, Limeira'),
('Mariana Santos', '888.999.000-11', '13482151', '1991-08-18 00:00:00', 'mariana.santos@email.com', 'senhaForte130', 'Rua das Violetas, 80, Jardim Nobre, Limeira'),
('Gabriel Pereira', '999.000.111-22', '13484331', '1996-06-22 00:00:00', 'gabriel.pereira@email.com', 'senhaForte131', 'Avenida Secundaria, 90, Parque das Nações, Limeira'),
('Aline Ribeiro', '000.111.222-33', '13486101', '1994-04-28 00:00:00', 'aline.ribeiro@email.com', 'senhaForte132', 'Rua dos Jasmins, 100, Vila Nova, Limeira');
INSERT INTO Funcionarios (nome_funcionario, CPF_funcioario, CEP_funcionario, data_nasc_funcionario, email_funcionario, senha_funcionario, endereco_funcionario) VALUES
('Roberto Mendes', '111.000.999-88', '13480012', '1987-12-01 00:00:00', 'roberto.mendes@email.com', 'senhaNova11', 'Rua dos Sabias, 110, Centro, Limeira'),
('Vanessa Lima', '222.111.000-99', '13482152', '1999-02-14 00:00:00', 'vanessa.lima@email.com', 'senhaNova12', 'Rua das Begônias, 120, Jardim Nobre, Limeira'),
('Thiago Barbosa', '333.222.111-00', '13484332', '1990-10-03 00:00:00', 'thiago.barbosa@email.com', 'senhaNova13', 'Avenida Terciaria, 130, Parque das Nações, Limeira');

select * from Funcionarios;

INSERT INTO Clientes (nome_cliente, CPF_cliente, CEP_cliente, data_nasc_cliente, email_cliente, endereco_cliente, senha_cliente) VALUES
('Ana Paula Vieira', '123.456.789-10', '13480100', '1999-03-08 00:00:00', 'ana.vieira@email.com', 'Rua dos Pássaros, 1, Centro', 'clienteSenha1'),
('Bruno Carvalho', '234.567.891-01', '13480200', '1985-12-11 00:00:00', 'bruno.carvalho@email.com', 'Rua das Águias, 2, Vila Pires', 'clienteSenha2'),
('Camila Ferreira', '345.678.910-12', '13482300', '2001-06-25 00:00:00', 'camila.ferreira@email.com', 'Avenida dos Ventos, 3, Jardim Paulista', 'clienteSenha3'),
('Diego Rocha', '456.789.101-23', '13484400', '1993-09-01 00:00:00', 'diego.rocha@email.com', 'Rua da Paz, 4, Cidade Jardim', 'clienteSenha4'),
('Eduarda Gomes', '567.891.012-34', '13486500', '1997-10-14 00:00:00', 'eduarda.gomes@email.com', 'Travessa da Harmonia, 5, Vila Glória', 'clienteSenha5'),
('Felipe Azevedo', '678.910.123-45', '13480110', '1980-07-19 00:00:00', 'felipe.azevedo@email.com', 'Rua dos Canários, 6, Centro', 'clienteSenha6'),
('Giovanna Barros', '789.101.234-56', '13480220', '2003-02-02 00:00:00', 'giovanna.barros@email.com', 'Rua das Gaivotas, 7, Vila Pires', 'clienteSenha7'),
('Heitor Correia', '891.012.345-67', '13482330', '1975-04-17 00:00:00', 'heitor.correia@email.com', 'Avenida da Brisa, 8, Jardim Paulista', 'clienteSenha8'),
('Isabela Dias', '910.123.456-78', '13484440', '1996-08-21 00:00:00', 'isabela.dias@email.com', 'Rua da Alegria, 9, Cidade Jardim', 'clienteSenha9'),
('João Mendes', '012.345.678-90', '13486550', '2000-11-29 00:00:00', 'joao.mendes@email.com', 'Travessa da Amizade, 10, Vila Glória', 'clienteSenha10');
INSERT INTO Clientes (nome_cliente, CPF_cliente, CEP_cliente, data_nasc_cliente, email_cliente, endereco_cliente, senha_cliente) VALUES
('Larissa Moreira', '112.233.445-56', '13480111', '1998-05-20 00:00:00', 'larissa.moreira@email.com', 'Rua dos Bem-te-vis, 11, Centro', 'clienteSenha11'),
('Marcos Vinicius', '223.344.556-67', '13482333', '1989-01-15 00:00:00', 'marcos.vinicius@email.com', 'Avenida da Neblina, 12, Jardim Paulista', 'clienteSenha12'),
('Natália Fernandes', '334.455.667-78', '13486555', '2002-09-09 00:00:00', 'natalia.fernandes@email.com', 'Travessa da União, 13, Vila Glória', 'clienteSenha13');

select * from Clientes;

INSERT INTO Produtos (tipo_produtos, nome_produtos, quant_produtos, data_venc_produtos) VALUES
('Suplemento', 'Whey Protein Isolado 900g - Baunilha', 50, '2027-09-01 00:00:00'),
('Suplemento', 'Creatina Monohidratada 300g', 120, '2028-05-15 00:00:00'),
('Vestuário', 'Camiseta Dry Fit Masculina - Preta M', 80, '2999-12-31 00:00:00'),
('Vestuário', 'Top Fitness Feminino - Rosa P', 75, '2999-12-31 00:00:00'),
('Acessório', 'Garrafa Térmica 750ml', 200, '2999-12-31 00:00:00'),
('Acessório', 'Corda de Pular com Rolamento', 150, '2999-12-31 00:00:00'),
('Alimento', 'Barra de Proteína - Chocolate', 300, '2026-12-20 00:00:00'),
('Suplemento', 'BCAA 2:1:1 200 Cápsulas', 90, '2027-11-10 00:00:00'),
('Acessório', 'Luva de Musculação com Munhequeira', 60, '2999-12-31 00:00:00'),
('Vestuário', 'Legging Feminina - Azul G', 85, '2999-12-31 00:00:00');
INSERT INTO Produtos (tipo_produtos, nome_produtos, quant_produtos, data_venc_produtos) VALUES
('Suplemento', 'Glutamina 300g', 110, '2028-10-20 00:00:00'),
('Alimento', 'Pasta de Amendoim Integral 500g', 250, '2026-08-15 00:00:00'),
('Acessório', 'Monitor Cardíaco de Pulso', 40, '2999-12-31 00:00:00');

select * from Produtos;

INSERT INTO Modalidades (nome_modalidades, tipo_modalidades, id_funcionario) VALUES
('Musculação', 'Treinamento de Força', 1),
('Cross Training', 'Alta Intensidade', 2),
('Yoga', 'Mente e Corpo', 3),
('Pilates', 'Flexibilidade e Força', 4),
('Zumba', 'Dança Aeróbica', 5),
('Spinning', 'Cardiovascular', 6),
('Treinamento Funcional', 'Força e Resistência', 7),
('Boxe', 'Artes Marciais', 8),
('Natação Adulto', 'Aquático', 9),
('Avaliação Física', 'Serviço', 10);
INSERT INTO Modalidades (nome_modalidades, tipo_modalidades, id_funcionario) VALUES
('Jump', 'Cardiovascular', 11),
('Alongamento e Flexibilidade', 'Mente e Corpo', 12),
('Hidroginástica', 'Aquático', 13);

select * from Modalidades;

INSERT INTO Agendamentos (data_agendamentos, id_funcionario, id_modalidades) VALUES
('2025-10-20 08:00:00', 3, 3), -- Yoga com Fernando
('2025-10-21 18:30:00', 5, 5), -- Zumba com Daniela
('2025-10-22 09:00:00', 4, 4), -- Pilates com Ricardo
('2025-10-23 19:00:00', 2, 2), -- Cross Training com Beatriz
('2025-10-24 10:00:00', 10, 10), -- Avaliação Física com Aline
('2025-10-27 17:00:00', 8, 8), -- Boxe com Mariana
('2025-10-28 07:00:00', 6, 6), -- Spinning com Juliana
('2025-10-29 15:00:00', 7, 7), -- Treinamento Funcional com Lucas
('2025-10-30 20:00:00', 9, 9), -- Natação com Gabriel
('2025-11-03 11:30:00', 10, 10); -- Avaliação Física com Aline
INSERT INTO Agendamentos (data_agendamentos, id_funcionario, id_modalidades) VALUES
('2025-11-04 18:00:00', 11, 11), -- Jump com Roberto
('2025-11-05 09:30:00', 12, 12), -- Alongamento com Vanessa
('2025-11-06 16:00:00', 13, 13); -- Hidroginástica com Thiago

select * from Agendamentos;

INSERT INTO Planos (beneficios_planos, nome_planos, id_cliente, id_funcionario) VALUES
('renovamento automatico, acesso a equipamentos', 'plano basico', 1, 1),
('renovamento automatico, acesso a equipamentos', 'plano casal', 2, 2),
('acesso as academias, renovamento automatico, treine com 5 amigos, acesso a massagem, acesso a equipamentos, avaliação fisica', 'plano premium', 3, 3);

<<<<<<< HEAD
select * from Planos;
=======
select * from Planos;

ALTER TABLE Clientes
ADD COLUMN perfil_acesso VARCHAR(10) NOT NULL DEFAULT 'cliente';
>>>>>>> 92395a3885dccbf2d90b0cd9dc09a04784b851dc
