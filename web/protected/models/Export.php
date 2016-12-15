<?php
class Export
{
    public static $db;


    public static function db() {
        self::$db = self::getDbConnectionType();
        if(self::$db instanceof CDbConnection){
            return self::$db;
        } else {
            throw new CDbException('Configução de exportação inválida.');
        }
    }

    /** 
     * Retorna o componente de conexão para o tipo de banco de dados
     * definido para exportação.
     */
    public static function getDbConnectionType()
    {
        $active = Configuracao::getActive();
        $host = $active->exportHost;
        $base = $active->exportDatabase;
        $port = $active->exportPort;
        $user = $active->exportUser;
        $pass = $active->exportPwd;

        if ($active->isMySqlExport()) {
            $component = [
                'class' => 'CDbConnection',
                'connectionString' => "mysql:host={$host};port={$port};dbname={$base}",
                'username' => $user,
                'password' => $pass,
                'emulatePrepare' => true,
            ];

        } elseif($active->isSqlServerExport()) {
            # TODO: usar definição de porta
            $component = array(
                'class' => 'CDbConnection',
                'connectionString' => "dblib:host={$host};dbname={$base};",
                'username' => $user,
                'password' => $pass,
            );
        } else {
            # TODO: outros drivers cubrid,pgsql,mysqli,mysql,sqlite,sqlite2,mssql,dblib,sqlsrv,oci
            throw new CDbException("Tipo de exportação '{$active->exportType}' desconhecido.");
        }

        return YiiBase::createComponent($component);
    }

}
