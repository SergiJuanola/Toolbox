<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';
require_once 'class.pdotool.php';

/**
* Base example class for Tools
*/
class Blog extends Builder {

	const MODEL_ASSOC = FALSE;
	const NO_FIELD = FALSE;

	/**
	* Default properties.
	*/
	public static $default = array(
		'pdo' => NULL,
		'match' => NULL,
		'model' => self::MODEL_ASSOC,
		'table' => 'post',
		'postsPerPage' => 4,
		'excerpt' => FALSE,
		'langField' => self::NO_FIELD,
		'slugField' => self::NO_FIELD,
		'dateField' => self::NO_FIELD,
		'idField' => 'id',
		'contentField' => 'content',
	);

	/**
	* Building method
	* @param array $config The config array
	* @see Builder::build()
	*/
	public static function build($config = array()) {
		$self = new self($config);
		if(empty($self->pdo))
			$self->pdo = Pdotool::build();
		return $self;
	}

	public function getPost($slug, $lang = NULL)
	{
		$sql = "SELECT * FROM `{$this->table}` ";
		$whereFields = array();
		$whereValues = array();

		if(!empty($this->slugField))
		{
			$whereFields[] = "`{$this->slugField}` = :{$this->slugField}";
			$whereValues[":{$this->slugField}"] = $slug;
		}
		else
		{
			return NULL;
		}
		if(!empty($this->langField) && !empty($lang))
		{
			$whereFields[] = "`{$this->langField}` = :{$this->langField}";
			$whereValues[":{$this->langField}"] = $lang;
		}
		else if(!empty($this->langField) && !empty($this->match))
		{
			$whereFields[] = "`{$this->langField}` = :{$this->langField}";
			$whereValues[":{$this->langField}"] = $this->match->getLocale();
		}

		if(!empty($whereFields))
			$sql .= " WHERE ".implode(" AND ", $whereFields);

		$sql .= " LIMIT 1";

		$statement = $this->pdo->prepare($sql);
		$statement->execute($whereValues);

		if($this->model == self::MODEL_ASSOC)
		{
			$post = $statement->fetch();
		}
		else
		{
			$post = $statement->fetchObject($this->model);
		}
		return $post;
	}

	public function getPostById($id, $lang = NULL)
	{
		$sql = "SELECT * FROM `{$this->table}` ";
		$whereFields = array();
		$whereValues = array();

		$whereFields[] = "`{$this->idField}` = :{$this->idField}";
		$whereValues[":{$this->idField}"] = $id;

		if(!empty($this->langField) && !empty($lang))
		{
			$whereFields[] = "`{$this->langField}` = :{$this->langField}";
			$whereValues[":{$this->langField}"] = $lang;
		}
		else if(!empty($this->langField) && !empty($this->match))
		{
			$whereFields[] = "`{$this->langField}` = :{$this->langField}";
			$whereValues[":{$this->langField}"] = $this->match->getLocale();
		}

		if(!empty($whereFields))
			$sql .= " WHERE ".implode(" AND ", $whereFields);

		$sql .= " LIMIT 1";

		$statement = $this->pdo->prepare($sql);
		$statement->execute($whereValues);

		if($this->model == self::MODEL_ASSOC)
		{
			$post = $statement->fetch();
		}
		else
		{
			$post = $statement->fetchObject($this->model);
		}
		return $post;
	}

	public function getPosts($page = 0, $excerpted = TRUE)
	{
		$statement = $this->generateListStatement($page);
		if($this->model == self::MODEL_ASSOC)
		{
			$posts = $statement->fetchAll();
			if($excerpted)
			{
				foreach ($posts as &$post) {
					$post = $this->excerptAssoc($post);
				}
			}
		}
		else
		{
			$posts = $statement->fetchAll(PDO::FETCH_CLASS, $this->model);
			if($excerpted)
			{
				foreach ($posts as &$post) {
					$post = $this->excerptClass($post);
				}
			}
		}

		return $posts;
	}

	private function generateListStatement($page = 0)
	{
		$sql = "SELECT * FROM `{$this->table}` ";
		$whereFields = array();
		$whereValues = array();
		if(!empty($this->match) && $this->langField !== self::NO_FIELD)
		{
			$whereFields[] = "`{$this->langField}` = :{$this->langField}";
			$whereValues[":{$this->langField}"] = $this->match->getLocale();
		}
		if(!empty($whereFields))
			$sql .= " WHERE ".implode(" AND ", $whereFields);

		if($this->dateField !== self::NO_FIELD)
			$sql .= " ORDER BY `{$this->dateField}` DESC";
		$sql .= " LIMIT {$this->postsPerPage} OFFSET ".($this->postsPerPage*$page);

		$statement = $this->pdo->prepare($sql);
		$statement->execute($whereValues);
		return $statement;
	}

	private function excerptClass($post) {
		if($this->excerpt == FALSE)
			return $post;
		elseif(is_string($this->excerpt))
		{
			$post->{$this->contentField} = $post->{$this->excerpt};
		}
		elseif(is_int($this->excerpt))
		{
			$content = strip_tags($post->{$this->contentField});
			if(strlen($content)>$this->excerpt)
				$content = substr($content, 0, ($this->excerpt-1))."&hellip;";
			$post->{$this->contentField} = $content;
		}
		return $post;
	}

	private function excerptAssoc($post) {
		if($this->excerpt == FALSE)
			return $post;
		elseif(is_string($this->excerpt))
		{
			$post[$this->contentField] = $post[$this->excerpt];
		}
		elseif(is_int($this->excerpt))
		{
			$content = strip_tags($post[$this->contentField]);
			if(strlen($content)>$this->excerpt)
				$content = substr($content, 0, ($this->excerpt-1))."&hellip;";
			$post[$this->contentField] = $content;
		}
	}
}