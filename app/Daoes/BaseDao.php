<?php

namespace App\Daoes;

use App\Utils\DateUtils;
use DB;

class BaseDao
{
    /**
     * save 新增或更新数据并保存对应的操作日记
     *
     * @param Illuminate\Database\Eloquent\Model $model  要操作的对像
     * @param int $userId              操作员
     * @return Illuminate\Database\Eloquent\Model $model
     */
    public static function save($model, $userId = null)
    {
        if ($model->exists) {
            self::saveUpdateLog($model, $userId, self::generateTablePrefix());
            if (!$model->save()) {
                return null;
            }
            return $model;
        } else {
            if (!$model->save()) {
                return null;
            }
            self::saveInsertLog($model, $userId, self::generateTablePrefix());
            return $model;
        }
    }

    /**
     * delete 逻辑删除数据并保存对应的操作日记
     *
     * @param Illuminate\Database\Eloquent\Model $model  要操作的对像
     * @param int $userId              操作员
     * @return boolean
     */
    public static function delete($model, $userId = null)
    {
        if ($model == null) {
            return false;
        }

        if (count($model) == 0) {
            return true;
        }

        if (isset($model[0])) {
            // $model是->get()方式获取
            foreach ($model as $mod) {
                self::saveDeleteLog($mod, $userId, self::generateTablePrefix());
                if (!$mod->delete()) {
                    return false;
                }
            }

            return true;
        } else {
            // $model是->first()或->find($id)方式获取
            self::saveDeleteLog($model, $userId, self::generateTablePrefix());
            return $model->delete();
        }
    }

    /**
     * saveInsertLog 生成新增数据的操作日记
     * 在model对象save方法后再调用
     * @param Illuminate\Database\Eloquent\Model $model  要操作的对像
     * @param int $userId              操作员
     * @param string $tablePrefix      表前缀
     * @return boolean
     */
    public static function saveInsertLog($model, $userId = null, $tablePrefix)
    {
        $logType   = config('statuses.systemLog.type.add.code');
        $systemLog = self::saveLog($model, $logType, $userId, $tablePrefix);

        if ($systemLog == null) {
            return false;
        }
        return true;
    }

    /**
     * saveUpdateLog 生成更新数据的操作日记
     *
     * 在model对象save方法后再调用
     * @param Illuminate\Database\Eloquent\Model $model  要操作的对像
     * @param int $userId              操作员
     * @param string $tablePrefix      表前缀
     * @return boolean
     */
    public static function saveUpdateLog($model, $userId = null, $tablePrefix)
    {
        // 验证模型有没有数据被修改过，没有被修改过的话就直接返回，不用记录日记
        if (!$model->isDirty()) {
            return false;
        }

        $logType   = config('statuses.systemLog.type.update.code');
        $systemLog = self::saveLog($model, $logType, $userId, $tablePrefix);

        if ($systemLog == null) {
            return false;
        }

        return self::saveLogDetail($model, $systemLog->id, $tablePrefix);
    }

    /**
     * saveDeleteLog 生成删除数据的操作日记
     * 在model对象save方法后再调用
     * @param Illuminate\Database\Eloquent\Model $model  要操作的对像
     * @param int $userId              操作员
     * @param string $tablePrefix      表前缀
     * @return boolean
     */
    public static function saveDeleteLog($model, $userId = null, $tablePrefix)
    {
        $logType = config('statuses.systemLog.type.delete.code');
        return self::saveLog($model, $logType, $userId, $tablePrefix);
    }

    /**
     * save the log to database 生成删除数据的操作日记
     *
     * @param Illuminate\Database\Eloquent\Model $model  要操作的对像
     * @param string $logType                            操作类型
     * @param int $userId              操作员
     * @param string $tablePrefix      表前缀
     * @return Illuminate\Database\Eloquent\Model $systemLog
     */
    public static function saveLog($model, $logType, $userId = null, $tablePrefix)
    {
        $tableName  = $model->getTable();
        $tableInfos = DB::select('show table status where name = "' . $tableName . '"');

        $systemLog               = self::generateModel($tablePrefix);
        $systemLog->type         = $logType;
        $systemLog->table_id     = $model->id;
        $systemLog->table_name   = $tableName;
        $systemLog->table_remark = $tableInfos[0]->Comment;

        if ($userId != null) {
            $systemLog->user_id = $userId;
        }
        if (!$systemLog->save()) {
            return null;
        }
        return $systemLog;
    }

    /**
     * save the log detail to database
     *
     * @param Illuminate\Database\Eloquent\Model $model          更改的的对像
     * @param String                             $systemLogId
     * @param String                             $tablePrefix
     * @return boolean
     */
    public static function saveLogDetail($model, $systemLogId = 0, $tablePrefix)
    {
        if ($model == null || $systemLogId == 0) {
            return null;
        }

        $tableName = $model->getTable();
        //查询字段注释
        $tableFields = DB::select('show full columns from ' . $tableName);
        $comments    = array();
        foreach ($tableFields as $tableField) {
            $comments[$tableField->Field] = $tableField->Comment;
        }
        $oldValues = $model->getOriginal();
        $newValues = $model->getAttributes();

        // 前后信息进行比较
        $insertDatas = array();
        $date        = DateUtils::newDate('Y-m-d H:i:s');
        foreach ($comments as $key => $value) {
            if (isset($newValues[$key]) && isset($oldValues[$key]) && strval($newValues[$key]) !== strval($oldValues[$key])) {
                $data = array(
                    'created_at'    => $date,
                    'updated_at'    => $date,
                    'system_log_id' => $systemLogId,
                    'field'         => $key,
                    'field_comment' => $value,
                    'old_value'     => $oldValues[$key],
                    'new_value'     => $newValues[$key],
                );
                array_push($insertDatas, $data);
            }
        }
        //　保存有修改的记录
        $count = DB::table($tablePrefix . 'system_log_details')->insert($insertDatas);
        if ($count < 1) {
            return false;
        }
        return true;
    }

    /**
     * 生成表前缀
     *
     * @return string
     */
    public static function generateTablePrefix()
    {
        $tablePrefix = 'user_';

        $actionName = request()->route()->getActionName();
        if (strpos($actionName, 'App\\Http\\Controllers\\Admins') !== false) {
            $tablePrefix = 'admin_';
        } elseif (strpos($actionName, 'App\\Http\\Controllers\\Merchants') !== false) {
            $tablePrefix = 'merchant_';
        }

        return $tablePrefix;
    }

    /**
     * 生成对象
     * @param  string $tablePrefix
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public static function generateModel($tablePrefix)
    {
        if ($tablePrefix == 'admin_') {
            return new \App\Models\Admins\SystemLog();
        } elseif ($tablePrefix == 'merchant_') {
            return new \App\Models\Merchants\SystemLog();
        } else {
            return new \App\Models\Users\SystemLog();
        }
    }
}
