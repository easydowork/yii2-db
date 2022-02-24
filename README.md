### 修改于yii2框架,独立的数据库连接组件
```php
<?php
require(__DIR__ . '/vendor/autoload.php');

use easydowork\db\Connection;
use easydowork\db\Query;

$connect = new Connection([
    'dsn' => 'mysql:host=127.0.0.1;dbname=test',
    'username' => 'test',
    'password' => 'test',
    'charset' => 'utf8',
]);
$data = (new Query())->from('user')->all($connect);
print_r($data);
$connect->close();
```