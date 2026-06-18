<?php
namespace App\Model;

use App\Auth;
use App\Cache;
use DateUtility;
use Exception;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;
use HardeepVicky\QueryBuilder\Join;
use HardeepVicky\QueryBuilder\Condition;

class BaseModel
{
    public $id;
    private String $table;
    public \Mysql $mysql;
    public Cache $cache;
    public Array $validationErrors = [], $validations = [], $tableFields = [], $children = [];
    public $date_fields = [];

    public function __construct()
    {
        global $mysql;

        $this->table = camelToSnake(str_class_name_without_namespace(static::class));
        $this->mysql = $mysql;

        $this->cache = new Cache(str_class_name_without_namespace(static::class));
    }

    public function getTable()
    {
        return $this->table;
    }

    public function find($fields = [], $conditions = [], $order_by = "id", $order_dir = "ASC", $start = 0, $limit = null)
    {
        $qs = new QuerySelect(new Table($this->table, str_class_name_without_namespace(static::class)));
        if ($fields)
        {
            $qs->setFieldList($fields);
        }

        if ($conditions)
        {
            $qs->setWhere(Condition::init("AND")->addList($conditions));
        }

        if ($order_by && $order_dir)
        {
            $qs->order($order_by, $order_dir);
        }

        if ($start)
        {
            $qs->setOffset($start);
        }

        if ($limit)
        {
            $qs->setLimit($limit);
        }

        return $this->mysql->select($qs->get());
    }

    public function findCount(Condition|null $condition)
    {
        $where = "";
        $str = $condition ? $condition->get() : "";

        if ($str)
        {
            $where = " WHERE " . $str;
        }

        $q = "SELECT COUNT(1) AS C from " . $this->table . $where;

        $records =  $this->mysql->select($q);

        return $records[0]['C'];
    }

    public function dateFields(&$records)
    {
        if ($this->date_fields)
        {
            foreach($records as $k => $record)
            {
                foreach($this->date_fields as $field => $out_format)
                {
                    if (isset($records[$k][$field]) && $records[$k][$field])
                    {
                        $records[$k][$field] = DateUtility::getDate($record[$field], $out_format);
                    }
                }
            }
        }
    }

    public function findQuery(QuerySelect $qs)
    {
        $records =  $this->mysql->select($qs->get());

        return $records;
    }

    public function findList($key, $value, $conditions = [], $order_by = "id", $order_dir = "ASC")
    {
        $qs = new QuerySelect(new Table($this->table));

        if ($conditions)
        {
            $qs->setWhere(Condition::init("AND")->addList($conditions));
        }

        $qs->field($key);
        $qs->field($value);
        if ($order_by && $order_dir)
        {
            $qs->order($order_by, $order_dir);
        }

        $records = $this->mysql->select($qs->get());

        $list = [];
        foreach($records as $record)
        {
            $list[$record[$key]] = $record[$value];
        }

        return $list;
    }

    public function findListCache($key, $value, $order_by = "id", $order_dir = "ASC")
    {
        $cache_key = "list_" . $key . "_" . $value;

        $list = $this->cache->get($cache_key);

        if ($list && is_array($list))
        {
            return $list;
        }

        $qs = new QuerySelect(new Table($this->table));

        $qs->field($key);
        $qs->field($value);
        if ($order_by && $order_dir)
        {
            $qs->order($order_by, $order_dir);
        }

        $records = $this->mysql->select($qs->get());

        $list = [];
        foreach($records as $record)
        {
            $list[$record[$key]] = $record[$value];
        }

        $this->cache->put($cache_key, $list);   

        return $list;
    }

    public function insert($data)
    {
        foreach($this->date_fields as $date_field => $out_format)
        {
            if (isset($data[$date_field]) && $data[$date_field])
            {
                $data[$date_field] = DateUtility::getDate($data[$date_field]);
            }
        }
        
        $this->validate($data);

        if ($this->validationErrors)
        {
            return false;
        }

        $table_fields = $this->getTableFields();

        foreach($data as $field => $value)
        {
            if (!in_array($field, $table_fields))
            {
                unset($data[$field]);
            }            
        }
        
        if (!$this->beforeInsert($data))
        {
            return false;
        }

        $field_list = $value_list = [];
        foreach($data as $field => $value)
        {
            $field_list[] = $field;
            $value_list[] = "'" . $value . "'";
        }

        $q = "INSERT INTO " . $this->table;
        $q .= "(" . implode(", ", $field_list) . ")";
        $q .= " VALUES(" . implode(", ", $value_list) . ")";

        $this->mysql->query($q);

        if ($this->mysql->getAffectedRows() > 0)
        {
            $records = $this->mysql->select("SELECT LAST_INSERT_ID() AS last_id");

            $this->id = $records[0]['last_id'];

            $this->afterSave();

            return true;
        }


        return false;
    }

    
    public function update($data)
    {
        foreach($this->date_fields as $date_field => $out_format)
        {
            if (isset($data[$date_field]) && $data[$date_field])
            {
                $data[$date_field] = DateUtility::getDate($data[$date_field]);
            }
        }

        $this->validate($data);

        if ($this->validationErrors)
        {
            return false;
        }
        
        $this->getTableFields();

        foreach($data as $field => $value)
        {
            if (!in_array($field, $this->tableFields))
            {
                unset($data[$field]);
            }            
        }
        
        if (!$this->beforeUpdate($data))
        {
            return false;
        }

        $value_list = [];
        foreach($data as $field => $value)
        {
            $value_list[] = "$field='" . $value . "'";
        }

        $q = "UPDATE " . $this->table;
        $q .= " SET " . implode(", ", $value_list);
        $q .= " WHERE id=" . $this->id;

        // d($q); exit;
        $this->mysql->query($q);

        $this->afterSave();

        return true;
        
    }

    public function afterSave()
    {
        $cache = new Cache(str_class_name_without_namespace(static::class));
        $cache->flush();
    }

    public function delete($id)
    {
        if (!$this->beforeDelete($id))
        {
            return false;    
        }

        $q = "DELETE FROM " . $this->table . " WHERE id = $id";

        $this->mysql->query($q);

        if ($this->mysql->getAffectedRows() > 0)
        {
            $this->afterDelete();
            return true;
        }


        return false;
    }

    public function afterDelete()
    {
        $cache = new Cache(str_class_name_without_namespace(static::class));
        $cache->flush();
    }

    public function beforeInsert(&$data)
    {
        if (in_array('created_at', $this->tableFields))
        {
            $data['created_at'] = date("Y-m-d H:i:s");
        }

        if (in_array('created_by', $this->tableFields))
        {
            $auth = new Auth();
            $data['created_by'] = $auth->user['id'] ?? 0;
        }

        return true;
    }

    public function beforeUpdate(&$data)
    {
        if (in_array('updated_at', $this->tableFields))
        {
            $data['updated_at'] = date("Y-m-d H:i:s");
        }

        if (in_array('updated_by', $this->tableFields))
        {
            $auth = new Auth();
            $data['updated_by'] = $auth->user['id'] ?? 0;
        }

        return true;
    }

    public function beforeDelete($id)
    {
        return true;
    }

    public function getTableFields()
    {
        if (!empty($this->tableFields))
        {
            return $this->tableFields;
        }

        $q = "
            SELECT COLUMN_NAME 
            FROM 
                INFORMATION_SCHEMA.COLUMNS 
            WHERE 
                TABLE_SCHEMA = '" . $this->mysql->db . "' 
                AND TABLE_NAME = '" . $this->table ."';
            ";

        $records = $this->mysql->select($q);

        $this->tableFields = array_column($records, 'COLUMN_NAME');

        return $this->tableFields;
    }

    public function validate($data)
    {
        foreach($data as $field => $value)
        {
            if (isset($this->validations[$field]))
            {
                $rules = $this->validations[$field];

                foreach($rules as $rule => $opt)
                {
                    if (is_numeric($rule))
                    {
                        $rule = $opt;
                    }

                    switch($rule)
                    {
                        case "required":
                            if (empty($value))
                            {
                                $msg = ucwords(str_function_name_to_human_text($field)) . " is required";
                                $this->validationErrors[$field][] = $msg;
                            }
                            break;

                        case "unique":
                            if (!empty($value))
                            {
                                $condition = Condition::init("AND")->add($field, $value);

                                if ($this->id)
                                {
                                    $condition->addCondition(Condition::init("NOT")->add("id", $this->id));
                                }

                                $count = $this->findCount($condition);

                                if ($count > 0)
                                {
                                    $msg = ucwords(str_function_name_to_human_text($field)) . " is already exist";
                                    $this->validationErrors[$field][] = $msg;
                                }
                            }
                            break;

                        case "confirm_password":
                            $other_field = is_array($opt) && $opt['other_field'] ? $opt['other_field'] : "confirm_password";

                            if (isset($data[$other_field]))
                            {
                                if ($data[$other_field] != $value)
                                {
                                    $msg = ucwords(str_function_name_to_human_text($field)) . " is not matched with " . ucwords(str_function_name_to_human_text($other_field));
                                    $this->validationErrors[$field][] = $msg;
                                }
                            }
                            else
                            {
                                $msg = "$other_field is not find in form data";
                                $this->validationErrors[$field][] = $msg;
                            }
                            break;

                        case "combo_unique":
                            if (!empty($value))
                            {
                                if (!isset($opt['other_fields']))
                                {
                                    $msg = "other_fields is not find in validation";
                                    $this->validationErrors[$field][] = $msg;
                                }

                                $condition = Condition::init("AND")->add($field, $value);

                                $other_field_name_list = [];
                                foreach($opt['other_fields'] as $other_field)
                                {
                                    if (isset($data[$other_field]))
                                    {
                                        $other_field_name_list[] = ucwords(str_function_name_to_human_text($other_field));
                                        $condition->add($other_field, $data[$other_field]);
                                    }
                                    else
                                    {
                                        $msg = "$other_field is not find in form data";
                                        $this->validationErrors[$field][] = $msg;
                                    }
                                }

                                if ($this->id)
                                {
                                    $condition->addCondition(Condition::init("NOT")->add("id", $this->id));
                                }

                                $count = $this->findCount($condition);

                                if ($count > 0)
                                {
                                    $msg = ucwords(str_function_name_to_human_text($field)) . " is already exist with " . implode(", ", $other_field_name_list);
                                    $this->validationErrors[$field][] = $msg;
                                }
                            }
                            break;
                    }
                }
            }
        }
    }



    public function created_by(&$records)
    {
        $user = new User();

        $user_id_list = array_unique(array_column($records, "created_by"));

        foreach($user_id_list as $k => $v)
        {
            if (!$v)
            {
                unset($user_id_list[$k]);
            }
        }

        if (empty($user_id_list))
        {
            return true;
        }

        $q = "SELECT id,name from " . $user->getTable() . " where id in (" . implode(",", $user_id_list) . ")";

        $user_records = $user->mysql->select($q);

        foreach($records as $k => $record)
        {
            foreach($user_records as $user_record)
            {
                if ($record['created_by'] == $user_record['id'])    
                {
                    $records[$k]['created_by'] = $user_record;
                }
            }    
        }
    }
    

    public function updated_by(&$records)
    {
        $user = new User();

        $user_id_list = array_unique(array_column($records, "updated_by"));

        foreach($user_id_list as $k => $v)
        {
            if (!$v)
            {
                unset($user_id_list[$k]);
            }
        }

        if (empty($user_id_list))
        {
            return true;
        }

        $q = "SELECT id,name from " . $user->getTable() . " where id in (" . implode(",", $user_id_list) . ")";

        $user_records = $user->mysql->select($q);

        foreach($records as $k => $record)
        {
            foreach($user_records as $user_record)
            {
                if ($record['updated_by'] == $user_record['id'])    
                {
                    $records[$k]['updated_by'] = $user_record;
                }
            }    
        }
    }
    
}