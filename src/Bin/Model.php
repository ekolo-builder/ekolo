<?php

    /**
     * Model principal
     * @author Don de Dieu BOLENGE <https://github.com/bolenge>
     */
    
    namespace Ekolo\Builder\Bin;

	/**
	 * Le model principal
	 */
	class Model
	{
        static $connections = [];
        
		protected $req = [];
		protected $table;
		protected $db;
		protected $primaryKey = 'id';
		protected $conf = 'default';

		/**
		 * Charge l'instance de l'objet Conf et l'objet PDO de la BDD
		 * @return void
		 */
		public function __construct()
		{
			$this->dbConnexion();
		}

        /**
         * Permet de se faire connecter à la base de données
         * @return void
         */
		public function dbConnexion()
		{
			// Connexion à la base de données
			$conf = \config('database');

			try {
				$pdo = new \PDO('mysql:host='.$conf['DB_HOST'].';dbname='.$conf['DB_DATABASE'],
					$conf['DB_USERNAME'], 
					$conf['DB_PASSWORD'],
					[\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
				);
				$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
				self::$connections[$this->conf] = $pdo;
				$this->db = $pdo;
				
			} catch (\PDOException $e) {
				die($e);
				die('Impossible de se connecter à la base de données.');
			}
		}

		/**
		 * 
		 */
		public function wherePredicate($where)
		{
			if (!empty($where)) {
				$sql .= ' WHERE ';
				$i = 1;
				$valuesExecute = [];
		
				if (!empty($where['and'])) {
					$sql .= '(';
		
					foreach ($where['and'] as $field => $value) {
						$i++;
						$valuesExecute[$field] = $value;
						$addAnd = ($i <= count($where['and'])) ? ' AND ' : '';
						$sql .= $field.' = :'.$field.$addAnd;
					}
		
					$sql .= ')';
				}
		
				if (!empty($where['or'])) {
					$i = 1;
					$sql .= !empty($where['and']) && ($i <= 2) ? ' OR ' : '';
					$sql .= '(';
		
					foreach ($where['or'] as $field => $value) {
						$i++;
						$addOr = $i <= count($where['or']) ? ' OR ' : '';
						$fieldExecute = key_exists($field, $valuesExecute) ? $field.$i : $field;
						$sql .= $field.' = :'.$fieldExecute.$addOr;
						$valuesExecute[$fieldExecute] = $value;
					}
		
					$sql .= ')';
				}
		
				debug($sql);
		
				$and = !empty($where['and']) ? implode('AND', $where['and']) : '';
			}
		}

		/**
		 * Permet d'ajouter un enregistrement dans une table
		 * @param $donnees array = Le tableau contenant les données qu'il faut ajouter
		 * @return true bool
		 */
		public function add($donnees, $table = null)
		{
			if ($table) {
				$this->table = $table;
			}

			// debug($this->db);

			if (!is_array($donnees)) {
				throw new Exception('La variable $donnees dans la methode Model::add() doit être un tableau associatif');
			}else{
				$fields = $values = $q = [];
				foreach ($donnees as $key => $value) {
					$fields[] = $key;
					$values[":$key"] = $value;
				}

				for ($i=0; $i < count($fields); $i++) { 
					$q[] = str_replace($fields[$i], ':'.$fields[$i], $fields[$i]);
				}

				$str_fields = implode(',', $fields);
				$str_q		= implode(',', $q);

				$sql = 'INSERT INTO '.$this->table.'('.$str_fields.') VALUES('.$str_q.')';

				$req = $this->db->prepare($sql);
				$req->execute($values);

				return $this->lastInsert();
			}
		}

		/**
		 * Permet de supprimer une entrée dans une table
		 * @param $data
		 * @return void
		 */
		public function delete($req, $table = null)
		{
			if ($table) {
				$this->table = $table;
			}

			if (!empty($req)) {
				$sql = 'DELETE FROM '.$table;

				if ($req['cond']) {
					$sql .= ' WHERE '.$req['cond'];
				}

				$req = $this->db->prepare($sql);
				$req->execute();
			}
		}

		/**
		 * Modifie un ou plusieurs enregistrements
		 * @param $data array = Les données à modifier
		 * @return true bool
		 */
		public function update($data, $table = null, $primaryKey = null)
		{
			if ($table) {
				$this->table = $table;
			}

			if ($primaryKey) {
				$this->primaryKey = $primaryKey;
			}

			$key = $this->primaryKey;
			$fields = $d = [];
			foreach ($data as $k => $v) {
				if ($k !== $this->primaryKey) {
					$fields[] = "$k=:$k";
				}
				
				$d[":$k"] = $v; 
			}

			$sql = 'UPDATE '.$this->table.' SET '.implode(',', $fields).' WHERE '.$key.'=:'.$key;

			$req = $this->db->prepare($sql);
			$req->execute($d);

			return true;
		}

		/**
		 * Permet de désactiver un enregistrement
		 * @param {Int} $id L'identifiant de l'enregistrement à désactiver
		 * @param {String} $table La table
		 * @return {Booleen}
		 */
		public function desactive($id, $table = null)
		{
			return $this->update([
				'id' => $id,
				'etat' => "0"
			], $table);
		}

		/**
		 * Permet de récuperer des infos dans la table
		 * @param array $req Les contraintes
		 * @return array $data Les données trouvées
		 */
		public function find($req = [], $table = null)
		{
			if ($table) {
				$this->table = $table;
			}

			$sql = 'SELECT ';

			$etat = "1";

			$sql .= isset($req['champs']) ? $req['champs'].' ' : '* ';
			$sql .= 'FROM ' . $this->table;
			$sql .= isset($req['cond']) ? ' WHERE '.$req['cond'] : '';
			$sql .= isset($req['order']) && !empty($req['order']) ? ' ORDER BY '.$req['order'] : '';
			$sql .= isset($req['limit']) && !empty($req['limit']) ? ' LIMIT '.$req['limit'] : '';

			// debug($sql);

			$req = $this->db->prepare($sql);
			$req->execute();
			
			if ($req) {
				return $req->fetchAll(\PDO::FETCH_OBJ);
			}

			return false;
		}

		/**
		 * Recherche tous les enregistrements
		 */
		public function findAll($req = [], $table = null)
		{
			return $this->find($req, $table);
		}

		/**
		 * Permet de récuperer seulement un enregistrement
		 * @param array $req = Les req qu'il faut
		 * @return object $data = les données trouvées
		 */
		public function findOne($req, $table = null)
		{
			return $this->find($req, $table) 
				   ? current($this->find($req, $table)) 
				   : false;
		}

		/**
		 * Recherche par rapport à l'id de cet enregistrement
		 * @param int $id L'id de cet enregistrement
		 * @param string $table La table où on fait cette recherche
		 */
		public function findById($id, $table = null)
		{
			return $this->find(['cond' => 'id='.$id], $table);
		}

		/**
		 * Recherche les données d'un enregistrement par rapport à son id
		 * @param int $id L'id de cet enregistrement
		 * @param string $table La table où on fait cette recherche
		 */
		public function findOneById($id, $table = null)
		{
			return $this->findOne(['cond' => 'id='.$id], $table);
		}
		
		/**
		 * Permet de compter le nombre des enregistremetns
		 * @param {Array} $req Les contraintes de la recherche des enregistrements
		 * @param {String} $table La table
		 */
		public function count($req = [], $table = null)
		{
			return count($this->find($req, $table));
		}

		/**
		 * Modifie l'attibut contenant le nom de la table
		 * @param string $table Le nom de la table
		 * @return void
		 */
		public function setTable($table)
		{
			$this->table = $table;
		}

		/**
		 * Renvoi le nom de la table du modèle
		 * @return string $table
		 */
		public function table()
		{
			return $this->table;
		}

		public function setPrimaryKey($key)
		{
			$this->primaryKey = $key;
		}

		public function primaryKey()
		{
			return $this->primaryKey;
		}

		/**
		 * Renvoi la dernière entrée dans la base de données
		 */
		public function lastInsert()
		{
			// debug($this->db->lastInsertId());
			return $this->findOne(['cond' => 'id='.$this->db->lastInsertId()]);
		}

		/**
		 * Vérifie si la valeur d'un champ est déjà utilisé
		 * @param string $table La table
		 * @param string $field Le champ à vérifier
		 * @param string $value La valeur
		 */
		public function isAlreadyUsed($field, $value, $table = null)
		{
			return (bool) $this->findOne(['cond' => "$field=".'\''.$value.'\' AND status="1"'], $table);
		}

		/**
		 * Vérifie si la valeur d'un champ est existe
		 * @param string $table La table
		 * @param string $field Le champ à vérifier
		 * @param string $value La valeur
		 * @return bool
		 */
		public function exists($field, $value, $table = null)
		{
			return (bool) $this->findOne(['cond' => "$field=".'\''.$value.'\''], $table);
		}

		/**
		 * Vérifie si l'email est déjà utilisé
		 * @param {String} $email
		 */
		public function emailIsAlreadyUsed($field, $value, $table = null)
		{
			$emails = $this->find(['champs' => $field], $table);

			foreach ($emails as $res) {
				if ($res->$field == $value) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Permet d'enregistrer des données (update si ca existe ou crée si non)
		 * @param array $data Les données à sauvegarger
		 * @param string $table
		 * @return mixed
		 */
		public function save(array $data, string $table = null)
		{
			return array_key_exists('id', $data) ? $this->update($data, $table) : $this->add($data, $table);
		}
	}