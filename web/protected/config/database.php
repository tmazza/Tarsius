<?php
return array(
    'class' => 'CDbConnection',
    'connectionString' => 'mysql:host=localhost;dbname=tarsius',
    'username' => 'root',
    'password' => '345',
    'emulatePrepare'=>true,  // necessário em algumas instalações do MySQL
    // 'connectionString'    => "sqlite:".__DIR__.'/../../tarsius.db',
);
