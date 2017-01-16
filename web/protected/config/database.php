<?php
return array(
    'class' => 'CDbConnection',
    'connectionString' => 'mysql:host=localhost;dbname=tarsius',
    'username' => 'username',
    'password' => 'password',
    'emulatePrepare'=>true,  // necessário em algumas instalações do MySQL
    // 'connectionString'    => "sqlite:".__DIR__.'/../../tarsius.db',
);
