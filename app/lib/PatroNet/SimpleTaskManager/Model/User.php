<?php

namespace PatroNet\SimpleTaskManager\Model;

use PatroNet\Core\Entity\ActiveRecordEntity;
use PatroNet\Core\Database\ActiveRecord;
use PatroNet\SimpleTaskManager\Rest\JsonDataEntity;


/**
 * Represents a user
 */
class User extends ActiveRecordEntity implements JsonDataEntity
{
    
    private static $oRepository = null;
    
    private $unsavedPassword = null;
    
    
    public function __construct(ActiveRecord $oActiveRecord)
    {
        parent::__construct($oActiveRecord);
    }
    
    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->oActiveRecord["email"] = $email;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->oActiveRecord["email"];
    }
    
    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        return $this->setEmail($username);
    }
    
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }
    
    public function setPassword($password)
    {
        $this->unsavedPassword = $password;
        return $this;
    }
    
    /**
     * @param string $password
     * @return boolean
     */
    public function checkPassword($password)
    {
        if (!is_null($this->unsavedPassword)) {
            return ($password == $this->unsavedPassword);
        } else {
            return password_verify($password, $this->oActiveRecord["hash_password"]);
        }
    }
    
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->oActiveRecord["name"] = $name;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->oActiveRecord["name"];
    }
    
    public function getLabel()
    {
        return "User " . $this->getId();
    }
    
    /**
     * @return string|null
     */
    public function getImageUrl()
    {
        return null;
    }
    
    /**
     * Deletes this user
     * 
     * @return boolean
     */
    public function delete()
    {
        // ...
        
        return parent::delete();
    }
    
    public function toJsonData($entityViewParameters)
    {
        $row = $this->getActiveRecord()->getRow();
        $imageUrl = $this->getImageUrl();
        return [
            "user_id" => $row["user_id"],
            "username" => $row["email"],
            "email" => $row["email"],
            "name" => $row["name"],
            "image" => $imageUrl,
        ];
    }
    
    public function save()
    {
        if (!is_null($this->unsavedPassword)) {
            $this->oActiveRecord["hash_password"] = password_hash($this->unsavedPassword, \PASSWORD_DEFAULT);
            $this->unsavedPassword = null;
        }
        
        if (!parent::save()) {
            return false;
        }
        
        // ...
        
        return true;
    }
    
    /**
     * Gets default user repository
     *
     * @return User\_Repository
     */
    public static function getRepository()
    {
        if (is_null(self::$oRepository)) {
            self::$oRepository = new User\_Repository();
        }
        return self::$oRepository;
    }
    
}


namespace PatroNet\SimpleTaskManager\Model\User;

use PatroNet\Core\Database\ActiveRecord;
use PatroNet\Core\Entity\TableRepository;
use PatroNet\SimpleTaskManager\Application;
use PatroNet\SimpleTaskManager\Model\User;
use PatroNet\SimpleTaskManager\Rest\JsonDataRepository;
use PatroNet\SimpleTaskManager\Rest\JsonDataTableRepositoryTrait;
use PatroNet\Core\Database\Table;


/**
 * @method User create()
 * @method User get(mixed $id)
 * @method User[]|\PatroNet\Core\Database\ResultSet getAll(int[] $idList = null, string[string] $order = null, mixed $limit = null)
 * @method User[]|\PatroNet\Core\Database\ResultSet getAllByFilter(mixed $filter = null, string[string] $order = null, mixed $limit = null)
 */
class _Repository extends TableRepository implements JsonDataRepository
{
    use JsonDataTableRepositoryTrait;
    
    public function __construct()
    {
        parent::__construct($oTable = Application::conn()->getTable("stm_user", "user_id", "user"));
        //$oTable->addRelation("[alias]", ["[table].[field]" => "[other table].[field]"], "[table name]");
    }
    
    /**
     * @return User|null
     */
    public function getByUsername($username)
    {
        return $this->getByEmail($username);
    }
    
    /**
     * @return User|null
     */
    public function getByEmail($email)
    {
        if (is_null($email)) {
            return null;
        }
        
        $oActiveRecord = $this->getTable()->getFirst(["email" => $email], null, null, Table::FETCH_ACTIVE);
        if (empty($oActiveRecord)) {
            return null;
        }
        
        return $this->wrapActiveRecordToEntity($oActiveRecord);
    }
    
    /**
     * @param \PatroNet\Core\Database\ActiveRecord $oActiveRecord
     * @return User
     */
    protected function wrapActiveRecordToEntity(ActiveRecord $oActiveRecord)
    {
        return new User($oActiveRecord);
    }
    
}