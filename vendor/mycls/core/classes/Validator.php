<?php

namespace mycls;

class Validator
{

    protected array $rules_list = ['required', 'min', 'max', 'email', 'preg', 'confirm', 'unique', 'uploadErrors', 'maxSize', 'allowedTypes', 'allowedExtensions', "inArray", 'maxLength'];
    protected array $errors = [];
    protected array $messages = [
        'required' => 'Поле обязательно для заполнения',
        'min' => 'Поле должно содержать минимум :rule_value: символов',
        'max' => 'Поле должно содержать не более :rule_value: символов',
        'email' => 'Неккоректный адрес электроной почты',
        'preg' => 'Содержатся или отсутсвуют символы',
        'confirm' => 'Данные не совпадают',
        'unique' => 'Пользователь с такими данными уже существует',
        'uploadErrors' => 'Ошибка при загрузке файла',
        'maxSize' => 'Размер файла слишком велик',
        'allowedTypes' => 'Неверный тип файла',
        'allowedExtensions' => 'Данное расширение не поддерживается',
        'inArray' => 'Неверная категория',
        'maxLength' => 'Превышено максимальное число файлов: :rule_value:'
    ];

    public function validation(array $data, array $rules)
    {
        foreach ($data as $fieldname => $value) {
            if (in_array($fieldname, array_keys($rules))) {
                $this->validate([
                    'fieldname' => $fieldname,
                    'value' => $value,
                    'rules' => $rules[$fieldname]
                ]);
            }
        }
    }

    protected function validate($field)
    {
        foreach ($field['rules'] as $rule => $rule_value) {
            if (in_array($rule, $this->rules_list)) {
                if (!call_user_func_array([$this, $rule], [$field['value'], $rule_value])) {
                    $this->addError($field['fieldname'], str_replace([":fieldname:", ':rule_value:'], [$field['fieldname'], $rule_value], $this->messages[$rule]));
                }
            }
        }
    }

    protected function addError($fieldname, $error)
    {
        $this->errors[$fieldname][] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors(string $fieldname)
    {
        return !empty($this->errors[$fieldname]);
    }

    protected function required($data, $rule_value)
    {
        return !empty($data);
    }

    protected function min($data, $rule_value)
    {
        return mb_strlen($data, 'UTF-8') >= $rule_value;
    }

    protected function max($data, $rule_value)
    {
        return mb_strlen($data, 'UTF-8') <= $rule_value;
    }

    protected function email($data, $rule_value)
    {
        return filter_var($data, FILTER_VALIDATE_EMAIL);
    }

    protected function preg($data, $rule_value)
    {
        return preg_match($rule_value, $data);
    }

    protected function confirm($data, $rule_value)
    {
        return $data == $rule_value;
    }

    protected function unique($data, $rule_value)
    {
        $db_config = require CONFIG . '/db.php';
        $db = Db::get_instance($db_config);
        $db->getConnection($db_config);

        return !$db->query("SELECT * FROM users WHERE {$rule_value} = :data", [':data' => $data])->fetchAll();
    }

    protected function uploadErrors($data, $rule_value)
    {
        return isset($data['error']) && $data['error'] === UPLOAD_ERR_OK;
    }

    protected function maxSize($data, $rule_value)
    {
        return isset($data['size']) && $data['size'] <= $rule_value;
    }

    protected function allowedTypes($data, $rule_value)
    {
        if (empty($data['tmp_name']) || !is_uploaded_file($data['tmp_name'])) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMimeType = finfo_file($finfo, $data['tmp_name']);

        $extension = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));

        $mimeMapping = [
            'pdf' => ['application/pdf', 'application/x-pdf'],
            'jpg' => ['image/jpeg', 'image/jpg'],
            'jpeg' => ['image/jpeg', 'image/jpg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'zip' => ['application/zip'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'txt' => ['text/plain'],
            'csv' => ['text/csv', 'text/plain']
        ];

        if (isset($mimeMapping[$extension])) {
            $acceptableMimes = $mimeMapping[$extension];

            foreach ($acceptableMimes as $acceptableMime) {
                if (in_array($acceptableMime, $rule_value)) {
                    return in_array($realMimeType, $acceptableMimes);
                }
            }
        }

        return in_array($realMimeType, $rule_value);
    }

    protected function allowedExtensions($data, $rule_value)
    {
        if (empty($data['name'])) {
            return false;
        }

        $extension = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
        return in_array($extension, $rule_value);
    }

    protected function inArray($data, $rule_value)
    {
        if (!in_array($data, $rule_value)) {
            return false;
        } else {
            return true;
        }
    }

    protected function maxLength($data, $rule_value) {
        if(count($data) > $rule_value) {
           return false; 
        }
        else {
            return true;
        }
    }
}
