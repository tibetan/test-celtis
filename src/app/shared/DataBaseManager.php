<?php
require_once('SettingManager.php');
require_once('LogHelper.php');

class DataBaseManager
{
    private $dsn;
    private $user;
    private $password;
    private $driverOpts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
    ];

    private $pdo;

    function __construct($key = null)
    {
        $localLog = LogHelper::log()->withName('DataBaseManager::__construct()');
        $localLog->addDebug('start');

        $settings = SettingManager::instance()->get('database');

        if (!empty($key)) {
            if (array_key_exists($key, $settings)) {
                $settings = $settings[$key];
            }
        }

        $this->dsn = $settings['dsn'];
        $this->user = $settings['user'];
        $this->password = $settings['password'];

        if (substr($this->dsn, 0, 6 ) === "sqlsrv") {
            unset($this->driverOpts[PDO::ATTR_ERRMODE]);
        }

        $this->pdo = new PDO($this->dsn, $this->user, $this->password, $this->driverOpts);
        $localLog->addDebug('finish');
    }

    public function select($stmt, $vars = [])
    {
        $localLog = LogHelper::log()->withName('DataBaseManager::select()');
        $localLog->addDebug('start', ['stmt' => $stmt, 'vars' => $vars]);

        try {
            $q = $this->pdo->prepare($stmt);

            $r = $q->execute($vars);

            if (false && $localLog->isHandling(Logger::DEBUG)) {
                ob_start();
                $q->debugDumpParams();
                $dump = ob_get_contents();
                ob_get_clean();

                $localLog->addDebug('SQL request', ['sql' => $dump]);
            }

            $localLog->addDebug('SQL response', ['value' => $r]);

            if ($r === FALSE) {
                $localLog->addError('SQL execute', ['value' => $q]);
                return FALSE;
            };

            $select = $q->fetchAll();
            $localLog->addDebug('finish', ['select' => $select]);
            return $select;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function insert($stmt, $vars, $const_vars = [], $skipDuplicateError = false)
    {
        $localLog = LogHelper::log()->withName('DataBaseManager::insert()');
        $localLog->addDebug('start', ['stmt' => $stmt, 'vars' => $vars, 'const_vars' => $const_vars, 'skipDuplicateError' => $skipDuplicateError]);

        try {
            $ids = [];

            $this->pdo->beginTransaction();

            $q = $this->pdo->prepare($stmt);

            foreach ($vars as $index => $var) {
                if ($index === 0) {

                    foreach ($const_vars as $k => $v) {
                        $q->bindValue(':' . $k, $v);
                    }

                    $data = [];
                    $i = 0;
                    foreach ($vars[0] as $k => $v) {
                        $data[$i] = $v;
                        $q->bindParam(':' . $k, $data[$i]);
                        $i++;
                    }

                } else {
                    $i = 0;
                    foreach ($var as $v) {
                        $data[$i++] = $v;
                    }
                }

                try {
                    $r = $q->execute();
                } catch (PDOException $e) {
                    if ($e->getCode() == '23000' && $skipDuplicateError) {
                        $localLog->addDebug('duplicate error', ['e' => $e]);
                        continue;
                    } else {
                        throw $e;
                    }
                }

                if (false && $localLog->isHandling(Logger::DEBUG)) {
                    ob_start();
                    $q->debugDumpParams();
                    $dump = ob_get_contents();
                    ob_get_clean();

                    $localLog->addDebug('SQL request', ['sql' => $dump]);
                }

                if ($r === FALSE) {
                    $localLog->addError('SQL execute', ['value' => $q]);
                    return FALSE;
                };

                $ids[] = $this->pdo->lastInsertId();
            }

            $this->pdo->commit();

            $localLog->addDebug('finish', ['ids' => $ids]);
            return $ids;
        } catch (PDOException $e) {
            $this->pdo->rollback();
            throw $e;
        }

    }

    public function delete($stmt, $vars = [])
    {
        $localLog = LogHelper::log()->withName('DataBaseManager::delete()');
        $localLog->addDebug('start', ['stmt' => $stmt, 'vars' => $vars]);

        try {
            $q = $this->pdo->prepare($stmt);

            $r = $q->execute($vars);

            if (false && $localLog->isHandling(Logger::DEBUG)) {
                ob_start();
                $q->debugDumpParams();
                $dump = ob_get_contents();
                ob_get_clean();

                $localLog->addDebug('SQL request', ['sql' => $dump]);
            }

            $localLog->addDebug('SQL response', ['value' => $r]);

            if ($r === FALSE) {
                $localLog->addError('SQL execute', ['value' => $q]);
                return FALSE;
            };

            $count = $q->rowCount();
            $localLog->addDebug('finish', ['count' => $count]);
            return $count;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function insertUpdate($stmt, $vars = [])
    {
        $localLog = LogHelper::log()->withName('DataBaseManager::insertUpdate()');
        $localLog->addDebug('start', ['stmt' => $stmt, 'vars' => $vars]);

        try {
            $q = $this->pdo->prepare($stmt);

            $r = $q->execute($vars);

            if (false && $localLog->isHandling(Logger::DEBUG)) {
                ob_start();
                $q->debugDumpParams();
                $dump = ob_get_contents();
                ob_get_clean();

                $localLog->addDebug('SQL request', ['sql' => $dump]);
            }

            $localLog->addDebug('SQL response', ['value' => $r]);

            if ($r === FALSE) {
                $localLog->addError('SQL execute', ['value' => $q]);
                return FALSE;
            };

            $count = $q->rowCount();
            $localLog->addDebug('finish', ['count' => $count]);
            return $count;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update($stmt, $vars, $const_vars = [])
    {
        $localLog = LogHelper::log()->withName('DataBaseManager::update()');
        $localLog->addDebug('start', ['stmt' => $stmt, 'vars' => $vars, 'const_vars' => $const_vars]);

        try {
            $count = 0;

            $this->pdo->beginTransaction();

            $q = $this->pdo->prepare($stmt);

            foreach ($vars as $index => $var) {
                if ($index === 0) {

                    foreach ($const_vars as $k => $v) {
                        $q->bindValue(':' . $k, $v);
                    }

                    $data = [];
                    $i = 0;
                    foreach ($vars[0] as $k => $v) {
                        $data[$i] = $v;
                        $q->bindParam(':' . $k, $data[$i]);
                        $i++;
                    }

                } else {
                    $i = 0;
                    foreach ($var as $v) {
                        $data[$i++] = $v;
                    }
                }

                try {
                    $r = $q->execute();
                } catch (PDOException $e) {
                    throw $e;
                }

                if (false && $localLog->isHandling(Logger::DEBUG)) {
                    ob_start();
                    $q->debugDumpParams();
                    $dump = ob_get_contents();
                    ob_get_clean();

                    $localLog->addDebug('SQL request', ['sql' => $dump]);
                }

                if ($r === FALSE) {
                    $localLog->addError('SQL execute', ['value' => $q]);
                    return FALSE;
                };

                $count = $count + $q->rowCount();
            }

            $this->pdo->commit();

            $localLog->addDebug('finish', ['count' => $count]);
            return $count;
        } catch (PDOException $e) {
            $this->pdo->rollback();
            throw $e;
        }

    }

}
