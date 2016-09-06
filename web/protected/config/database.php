<?php
return array(
    'class' => 'CDbConnection',
    'connectionString' => 'mysql:host=localhost;port=3389;dbname=tarsius',
    'username' => 'app',
    'password' => 'acf729fb32637844df1dbd9c',
    'emulatePrepare'=>true,  // necessário em algumas instalações do MySQL
    // 'connectionString'    => "sqlite:".__DIR__.'/../../tarsius.db',
);
