<?php

require_once('SettingManager.php');

class OperationResultType
{
    const ok = 0;
    const business_error = 1;
    const system_error = 2;
}

class OperationResult
{
    // ok, business, system
    public $type;
    public $code;
    public $data;
    public $defaultLanguage;

    public function __construct()
    {
        $this->defaultLanguage = SettingManager::instance()->get('default_language');
    }

    static public function makeOk($data = [], $code = OK)
    {
        return (new OperationResult())->ok($data, $code);
    }

    static public function makeBusinessFail($code, $data = [])
    {
        return (new OperationResult())->business_fail($code, $data);
    }

    static public function makeSystemFail($code, $data = [])
    {
        return (new OperationResult())->system_fail($code, $data);
    }

    private function ok($data, $code)
    {
        $localLog = LogHelper::log();

        if ($code === OK) {
            $localLog->addInfo(MessageManager::Get($code, 'log'), is_array($data) ? $data : ['data' => $data]);
        } else {
            $logData = is_array($data) ? $data : ['data' => $data];
            $logData['code'] = $code;
            $logData['message'] = MessageManager::Get($code, $this->defaultLanguage);
            $localLog->addWarning(MessageManager::Get($code, 'log'), $logData);

            $runtimeBusinessErrors[] = MessageManager::Get($code, $this->defaultLanguage);
            $runtimeBusinessErrors = array_unique($runtimeBusinessErrors);
        }

        return $this->result(OperationResultType::ok, $data, MessageManager::Get($code, 'code'));
    }

    private function business_fail($code, $data)
    {
        $localLog = LogHelper::log();
        $logData = [
            'data' => $data,
            'code' => $code,
            'message' => MessageManager::Get($code, $this->defaultLanguage),
        ];
        $localLog->addWarning(MessageManager::Get($code, 'log'), $logData);

        return $this->result(OperationResultType::business_error, $logData, MessageManager::Get($code, 'code'));
    }

    private function system_fail($code, $data)
    {
        $localLog = LogHelper::log();
        $logData = [
            'data' => $data,
            'code' => $code,
            'message' => MessageManager::Get($code, $this->defaultLanguage),
        ];
        $localLog->addError(MessageManager::Get($code, 'log'), $logData);

        return $this->result(OperationResultType::system_error, $logData, MessageManager::Get($code, 'code'));
    }

    private function result($type, $data, $code)
    {
        $this->type = $type;
        $this->data = $data;
        $this->code = $code;
        return $this;
    }

    public function json()
    {
        return json_encode($this->array(), JSON_UNESCAPED_UNICODE);
    }

    public function array()
    {
        if ($this->type === OperationResultType::ok) {
            return ['type' => $this->type, 'data' => $this->data];
        } else {
            return ['type' => $this->type, 'code' => $this->code, 'message' => $this->data['message']];
        }
    }

}
