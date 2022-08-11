<?php

    namespace APP\Models;

    use MF\Model\Model;

    class Tweet extends Model {
        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        //salver

        public function salvar() {
            $query = "INSERT INTO tc_tweets (id_usuario, tweet) VALUES (:id_usuario, :tweet)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));

            $stmt->execute();

            return $this;
        }

        //recuperar
        public function getAll() {
            $query = "SELECT t.id, t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
                        FROM tc_tweets t
                   LEFT JOIN tc_usuarios u
                   ON t.id_usuario = u.id
                       WHERE t.id_usuario = :id_usuario
                          OR t.id_usuario in (
                            SELECT id_usuario_seguindo FROM tc_usuarios_seguidores WHERE id_usuario = :id_usuario
                          )
                    ORDER BY t.data DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        //remover tweet
        public function removerTweet() {
            $query = "DELETE FROM tc_tweets WHERE id = :id_tweet";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_tweet', $this->__get('id'));
            $stmt->execute();

            return true;
        }

        //recuperar por pagina
        public function getPorPagina($limit, $offset) {
            $query = "SELECT t.id, t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
                        FROM tc_tweets t
                   LEFT JOIN tc_usuarios u
                   ON t.id_usuario = u.id
                       WHERE t.id_usuario = :id_usuario
                          OR t.id_usuario in (
                            SELECT id_usuario_seguindo FROM tc_usuarios_seguidores WHERE id_usuario = :id_usuario
                          )
                    ORDER BY t.data DESC
                    LIMIT
                        $limit
                    OFFSET
                        $offset";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        //recuperar total de tweets
        public function getTotalRegistros() {
            $query = "SELECT count(*) as total
                        FROM tc_tweets t
                   LEFT JOIN tc_usuarios u
                   ON t.id_usuario = u.id
                       WHERE t.id_usuario = :id_usuario
                          OR t.id_usuario in (
                            SELECT id_usuario_seguindo FROM tc_usuarios_seguidores WHERE id_usuario = :id_usuario
                          )";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }