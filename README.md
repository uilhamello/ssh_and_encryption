Instalação

Download via Github:
git clone git@github.com:uilhamello/ssh_and_encryption.git

Banco de dados

- Crie o banco de dados caso não o tenha:
	create database ssh_encrypt
  (o nome 'ssh_encrypt' é só uma sugestão, pode colar o que melhor para ti)

- Execute o dump da pasta : database/dump.sql 
  mysql -u [database_user] -p [new_database] < dump.sql
  Ex:
   mysql -u root -p ssh_encrypt < dump.sql

- Edite as informações de conexão no arquivo config/database.php

Exemplo:
"mysql_dev" => [
	"default" => true,
	"DRIVER" => "mysql",
	"HOST" => "localhost",
	"DBNAME" => "database_name",
	"USER" => "root",
	"PASSWORD" => ""

Pronto!


Função 1

- Conexão via SSH utilizando utilizando a extensão SSH2;

Model
- Foi criado uma classe statica chamada “Ssh2”, onde é efetuado todo processo de comunicação SSH.
Class: /libs/Helpers/Ssh2.php
app/Models/UserMachine.php : uma DTO da table 'user_machines' do banco de dados onde são armazenadas as máquinas que determinado usuario realizou conexão

Controller:
-  MachineController: gerencia todos os processos dessa função

View
- index.html : lista te todas as connexão realizada pelo usuario logado
- shell.html : tela de input de comandos para o SSH e output da resposta
     Comunica-se com o controller via Ajax.

Função 2

Criptografar e descriptografar texto: 
Tentativa de utilização da biblioteca 'openssl', porém não obtive sucesso na descriptografia.
Método utilizado: AES-256-CTR
Salt e IV Randomico e método bcrypt (blowfish) na geração da Hash

Helpers Files
/libs/Encryption/FactoryCrypt.php
/libs/Encryption/Crypt.php

Controller:
EncryptedTextController: gerencia todos os processos dessa função

View:
encrypted_text/index.html : input e output de dados que serão/forão criptografados

Função 3

Upload e  Auditoria de arquivos
Para encriptografia de arquivos foi utilizado a função 'sha256'

O rash original do momento do upload foi armazenado no banco de dados

- Ao fazer upload do mesmo arquivo: verifica se já há o hash registrado no banco de dados
se houver, se sim, verifica se o arquivo existem está como original
- Ao clicar em auditoria: verifica de o hash do arquivo no no diretorio segue igual ao armazenado no banco de dados.
Em ambos os casos apresenta output com resultado da auditoria realizada.

Model
/app/Models/UserFile.php: DTO da tabela 'user_files' do banco de dados

Helper:
/libs/Helpers/Crypt.php: criptografia do arquivo
/libs/Helpers/File.php: gerencia todo processo de upload 

 Controller
/app/Controllers/UserFileController.php : gerencia todo processo de Auditoria 

Views:
/app/views/user_file/index.html : input e output dos dados




Metodologias utilizadas
- POO
- MVC
- Factory na Model
- Active Record
- DTO
- Replace de dados dinamicos para deixar a View sem PHP

Bibliotecas extras do PHP
- openssl
- shh2

Bibliotecas extras front-end
 - Bower: gerenciador de pacotes front-end
- Jquery
- Bootstrap
- Datatables


Dependências de PHP 5.5
- password_hash
Referencias

ssh:
 - http://kvz.io/blog/2007/07/24/make-ssh-connections-with-php/

crypt:
- http://www.daemonology.net/blog/2009-06-11-cryptographic-right-answers.html
- http://blog.thiagobelem.net/criptografando-senhas-no-php-usando-bcrypt-blowfish
- http://code.tutsplus.com/tutorials/understanding-hash-functions-and-keeping-passwords-safe--net-17577




