# version-comparator: сравнение простых версий ПО

Поддержка версий PHP:
1. [x] 8.3
2. [ ] 7.4
3. [ ] 7.2

Сравнение двух версий

```php
\App\Comparator::gt('2.0.1', '1.0.1') // true
\App\Comparator::eq('1.67.0.9', '1.67.0.8') // false
\App\Comparator::lt('10.1.1', '10.1.2') // true
```

Сравнение среди множества версий

```php
$comparator = new \App\Comparator();
$comparator->pushVersion(['1.0.1', '2.0.1', '3.0.1']);

echo $comparator->getHighestVersion(); // 3.0.1
echo $comparator->getLowestVersion(); // 1.0.1
```
