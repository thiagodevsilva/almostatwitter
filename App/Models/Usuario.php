<?php

    namespace APP\Models;

    use MF\Model\Model;

    class Usuario extends Model {
        private $id;
        private $nome;
        private $email;
        private $senha;

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        //salvar
        public function salvar() {
            $query = "INSERT INTO tc_usuarios(nome, email, senha)VALUES(:nome, :email, :senha)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', $this->__get('nome'));
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha'));
            $stmt->execute();

            return $this;
        }

        //validar se o cadastro pode ser feito
        public function validarCadastro() {
            $valido = true;

            if (strlen($this->__get('nome')) < 3) {
                $valido = false;
            }

            if (strlen($this->__get('email')) < 3) {
                $valido = false;
            }

            if (strlen($this->__get('senha')) < 3) {
                $valido = false;
            }

            return $valido;
        }

        //recuperar um usuario por e-mail
        public function getUserEmail() {
            $query = "SELECT nome, email FROM tc_usuarios WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        //autenticar
        public function autenticar() {
            $query = "SELECT id, nome, email FROM tc_usuarios WHERE email = :email AND senha = :senha";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha'));

            $stmt->execute();

            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($usuario['id'] != '' & $usuario['nome'] != '') {
                $this->__set('id', $usuario['id']);
                $this->__set('nome', $usuario['nome']);
            }

            return $this;
        }

        //recuperar
        public function getAll() {
            $query = "SELECT u.id, u.nome, u.email, (
                SELECT count(*) FROM tc_usuarios_seguidores as us WHERE us.id_usuario = :id_usuario AND us.id_usuario_seguindo = u.id
            ) as seguindo_sn FROM tc_usuarios as u WHERE u.nome LIKE :nome AND u.id != :id_usuario";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%');
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        //info user
        public function getInfoUsuario() {
            $query = "SELECT nome FROM tc_usuarios WHERE id = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // qtde tweets
        public function getTotalTweets() {
            $query = "SELECT count(*) as total_tweets FROM tc_tweets WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // qtde seguindo
        public function getTotalSeguindo() {
            $query = "SELECT count(*) as total_seguindo FROM tc_usuarios_seguidores WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //qtde seguidores
        public function getTotalSeguidores() {
            $query = "SELECT count(*) as total_seguidores FROM tc_usuarios_seguidores WHERE id_usuario_seguindo = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }



    }


?>