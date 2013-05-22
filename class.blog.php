<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */
require_once 'class.builder.php';
require_once 'class.pdotool.php';

/**
* Blog post manager tool for Toolbox
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Builder
*/
class Blog extends Builder {

	const MODEL_ASSOC = FALSE;
	const NO_FIELD = FALSE;
	const ORDER_ASCENDING = "ASC";
	const ORDER_DESCENDING = "DESC";

	/**
	* Default properties.
	* @param Pdotool $pdo The Pdotool used to connect to the database.
	* @param Match $match The Match used here.
	* @param string $model The name of the class that holds each post, or MODEL_ASSOC if the return value is an associative array. Defaults to MODEL_ASSOC.
	* @param string $table The table name that holds each post. Defaults to 'post'.
	* @param int $postsPerPage The amount of posts per page. Defaults to 4.
	* @param mixed $excerpt The system Blog will use to generate an excerpt and store it in content. If $excerpt is a number, it will crop the $contentField to that amount of characters. If it is a string, it will use that as a field name of the table. If it is FALSE, it will use the $contentField itself. Defaults to FALSE.
	* @param string $langField The field name in the selected table that holds the language of the post. If NO_FIELD, no lang is set and will not be used. Defaults to NO_FIELD.
	* @param string $slugField The field name in the selected table that holds the slug of the post. If NO_FIELD, no slug is set and will not be used. Defaults to NO_FIELD.
	* @param string $dateField The field name in the selected table that holds the creation time of the post. If NO_FIELD, no date is set and will not be used. Note that this field is used for sorting. If you want to sort the posts using other criteria rather than Date, you can use it as well. Defaults to NO_FIELD.
	* @param string $idField The field name in the selected table that holds the id of the post. If NO_FIELD, no id is set and will not be used. Defaults to 'id'.
	* @param string $contentField The field name in the selected table that holds the content of the post. If NO_FIELD, no content is set and will not be used. Defaults to 'content'.
	* @link Pdotool
	* @link Match
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
		'dateOrder' => self::ORDER_DESCENDING,
		'idField' => 'id',
		'contentField' => 'content',
	);

	/**
	* Building method
	* @param array $config The config array
	* @link Builder::build()
	*/
	public static function build($config = array()) {
		$self = new self($config);
		if(empty($self->pdo))
			$self->pdo = Pdotool::build();
		return $self;
	}


	/**
	* Gets a post by its slug.
	* Get a post from the database, using its slug and language, if present.
	* @param string $slug The post slug
	* @param string $lang The language. If NULL, and $langField is defined, it will check Match for the current language. If no $langField is defined, each post is treated as coming from the same language. Defaults to NULL.
	* @return mixed The post matching the slug. Its class depends on the $model property (an associative array or a $model type). If slug is empty or a post is not found, returns NULL.
	* @link Match
	*/
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

	
	/**
	* Gets the next post, according to $dateField
	* @param Post $post The relative post
	* @return mixed The next post, or NULL if it hasn't
	*/
	public function getNextPost($post)
	{
		$comparator = "";
		switch ($this->dateOrder) {
			case self::ORDER_DESCENDING:
				$comparator = "<";
				break;
			case self::ORDER_ASCENDING:
			default:
				$comparator = ">";
				break;
		}
		$sql = "SELECT * FROM `{$this->table}` WHERE `{$this->dateField}` $comparator :{$this->dateField} ORDER BY {$this->dateField} {$this->dateOrder} LIMIT 1";

		$statement = $this->pdo->prepare($sql);


		if($this->model == self::MODEL_ASSOC)
		{
			$statement->execute(array(":{$this->dateField}"=>$post[$this->dateField]));
			$next = $statement->fetch();
		}
		else
		{
			$statement->execute(array(":{$this->dateField}"=>$post->{$this->dateField}));
			$next = $statement->fetchObject($this->model);
		}
		return $next;
	}

	
	/**
	* Gets the previous post, according to $dateField
	* @param Post $post The relative post
	* @return mixed The previous post, or NULL if it hasn't
	*/
	public function getPrevPost($post)
	{
		$comparator = "";
		switch ($this->dateOrder) {
			case self::ORDER_DESCENDING:
				$comparator = ">";
				$reorder = self::ORDER_ASCENDING;
				break;
			case self::ORDER_ASCENDING:
			default:
				$comparator = "<";
				$reorder = self::ORDER_DESCENDING;
				break;
		}
		$sql = "SELECT * FROM `{$this->table}` WHERE `{$this->dateField}` $comparator :{$this->dateField} ORDER BY {$this->dateField} $reorder LIMIT 1";

		$statement = $this->pdo->prepare($sql);


		if($this->model == self::MODEL_ASSOC)
		{
			$statement->execute(array(":{$this->dateField}"=>$post[$this->dateField]));
			$prev = $statement->fetch();
		}
		else
		{
			$statement->execute(array(":{$this->dateField}"=>$post->{$this->dateField}));
			$prev = $statement->fetchObject($this->model);
		}
		return $prev;
	}

	
	/**
	* Gets a post by its id. It is useful for grabbing the same post in another language.
	* Get a post from the database, using its id and language, if present.
	* @param mixed $id The post id.
	* @param string $lang The language. If NULL, and $langField is defined, it will check Match for the current language. If no $langField is defined, each post is treated as coming from the same language. Defaults to NULL.
	* @return mixed The post matching the id. Its class depends on the $model property (an associative array or a $model type). If a post is not found, returns NULL.
	* @link Match
	*/
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

	
	/**
	* Gets a list of posts, paginated and excerpted, if needed.
	* The post list is ordered by the $dateField field descending. It filters the posts by the current language, if a Match and $langField are present.
	* @param int $page The current page. The first page is 0. Defaults to 0.
	* @param boolean $excerpted TRUE if you want the content to be excerpted, FALSE otherwise. Defaults to TRUE. 
	* @return array The posts list, already filtered by page and language.
	* @link Match
	*/
	public function getPosts($page = 0, $excerpted = TRUE)
	{
		$statement = $this->generateListStatement($page);
		if($this->model == self::MODEL_ASSOC)
		{
			$posts = $statement->fetchAll(PDO::FETCH_ASSOC);
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

	
	/**
	* Create a criteria to filter the posts for Pdotool.
	* @param int $page The current page. The first page is 0. Defaults to 0.
	* @return PDOStatement The statement to filter the posts.
	*/
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
			$sql .= " ORDER BY `{$this->dateField}` {$this->dateOrder}";
		$sql .= " LIMIT {$this->postsPerPage} OFFSET ".($this->postsPerPage*$page);

		$statement = $this->pdo->prepare($sql);
		$statement->execute($whereValues);
		return $statement;
	}

	/**
	* Excerpt a post, coming in a class. The resulting excerpt is stored in $contentField
	* @param object $post The post to be excerpted.
	* @return object The post once excerpted.
	*/
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

	/**
	* Excerpt a post, coming in an associative array. The resulting excerpt is stored in $contentField
	* @param array $post The post to be excerpted.
	* @return array The post once excerpted.
	*/
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
		return $post;
	}
}