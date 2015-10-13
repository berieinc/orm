# PHP ORM for working with mySQL

```
$config = [
    'unix_socket' => 'path/to/unix',
    'host'        => 'localhost(127.0.0.1)',
    'username'    => 'root',
    'password'    => 'root',
    'dbname'      => 'root',
    'charset'     => 'utf8',
];
```

## runQuery() - self execute mysql request.
```
$orm = new \Berie\ORM($config);
```

```
$orm->runQuery("SELECT * FROM `foo`");
```

```
$orm->runQuery("DELETE FROM `foo` WHERE `id`='100'");
```

## getBuilder() - simple way to build query and execute.

```
$orm = new \Berie\ORM($config);
```
**getBuilder()->insert()**
```
$orm->getBuilder()
    ->insert('foo_table')
    ->set([
        'id' => 12,
        'di' => 'bar',
        'dd' => 'open',
    ])
    ->set('city', 'CA')
    ->getQuery();
```
**getBuilder()->update()**
```
$orm->getBuilder()
    ->update('foo_table')
    ->set('id', 23)
    ->set('city', 'LA')
    ->where('id', 12)
    ->getQuery();
```
**getBuilder()->delete()**
```
$orm->getBuilder()
    ->delete()
    ->from('foo_table')
    ->where('`id` IN (3,4,5) OR `city` LIKE "%CA%"')
    ->getQuery();
```
**getBuilder()->select()**
```
$select = $orm->getBuilder()
    ->select([`id`, `name`, `city`])
    ->from('foo_table')
    ->where('`id`=1 AND `name`=2');
```
**getBuilder()->select()->...->getArray()**
```
$select->getArray();
```
**getBuilder()->select()->...->getEntity()**
```
$select->getEntity();
```
**getBuilder()->select()->...->getCount()**
```
$select->getCount();
```

## getManager() - simple way to build query and execute.
**getManager()->__construct()**
```
$orm = new \Berie\ORM($config);
```

```
$manager = $orm->getManager('foo_table');
```
**getManager()->find()**
```
$entity = $orm->getManager('foo_table')
    ->find($id = 43);
```
**getManager()->findAll()**
```
$entity = $orm->getManager('foo_table')
    ->findAll();
```
**getManager()->findBy() AND getManager()->findOneBy()**
```
$entity = $orm->getManager('foo_table')
    ->findOneBy(['name' => 'foo']);
```
```
$entity = $orm->getManager('foo_table')
    ->findBy(['city' => 'CA']);
```
**save() AND remove()**
```
$entity = $orm->getManager('foo_table')
    ->find([33]);

$entity->setData([
     'name' => 'fooBar',
     'city' => 'SF',
     'email' => 'mail@email.com',
]);

$orm->save($entity);
```
```
$entities = $orm->getManager('foo_table')
    ->findAll();

foreach($entities as $entity) {
    $orm->remove($entity);
}
```
