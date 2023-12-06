# Ant colony optimization (Муравьиный алгоритм)

[English version](../README.md)

## Содержание

* [Введение](#introducion)
* [Демо](#demo)
* [Установка](#installing)
* [Использование](#use)
* [Настройка](#settings)
* [Производительность](#performance)
* [TSPLIB95](#tsplib95)
* [Терминология](#terminology)


---

## Введение <span id="introducion"></span>

Муравьиный алгоритм (ACO) позволяет решать вычислительные задачи способом нахождения хороших путей на графе.

Решаемая задача может быть как "Задачей коммивояжера", так и "О кратчайшем пути", или путем с некими ограничениями (Constrained Shortest Path First).  
Первые две задачи решены в данной библиотеке.

Существует множество стратегий и вариаций классического ACO алгоритма.  
Библиотека предоставляет реализацию Классического ACO и вариант с использованием Элитных муравьев.

Библиотека гибко расширяется и позволяет вам создать свои алгоритмы из мира ACO, и решать свои задачи.

Исходные данные поступают и в виде матрицы смежности и в виде списка нод (городов, узлов, вершин) с X и Y координатами.

Работа библиотеки протестирована на наборах данных [TSPLIB95](#tsplib95), для проверки эффективности и [производительности](#performance).

Кол-во муравьев, все коэффициенты и параматры, [настраиваются](#settings).


---

## Демо <span id="demo"></span>

Определение точек по стратегии ```DifferentColorsStrategy``` , **Рисунок 1**:
![Определение точек на изображении](https://raw.githubusercontent.com/mgrechanik/yii2-categories-and-tags/master/docs/images/categories.png "Определение точек на изображении")


Определение точек по стратегии ```ChoosenColorStrategy``` , **Рисунок 2**:
![Определение точек на изображении](https://raw.githubusercontent.com/mgrechanik/yii2-categories-and-tags/master/docs/images/categories.png "Определение точек на изображении карты США")

	
---
    
## Установка <span id="installing"></span>

#### Установка через composer:

Выполните
```
composer require --prefer-dist mgrechanik/ant-colony-optimization
```

или добавьте
```
"mgrechanik/ant-colony-optimization" : "~1.0.0"
```
в  `require` секцию вашего `composer.json` файла.



---

## Использование  <span id="use"></span> 

### Базовое API

1) **Создание Менеджера с нужными зависимостями**
```php
Manager::__construct(DistanceInterface $distanceStrategy = null, AFinder $finder = null, 
                     MathematicsInterface $mathematics = null, Task $task = null);
```
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- По умолчанию **Поисковик** будет устанавливаться в Классический ACO, а решаемая **Задача** - в Задачу Коммивояжера

2) **Загрузка данных в виде матрицы смежности**
```php
$manager->setMatrix(array $matrix, int $nameStart = 0)
```
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ```$nameStart``` - с какого номера именовать алиас имени ноды, для их внешнего имени-алиаса

3) **Загрузка данных в виде списка городов**
```php
$manager->setCities(City ...$cities)
```
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Данный список городов будет преобразован в матрицу смежности, расстояния вычислены по указанной Менеджеру стратегии.
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Если у города задано свойство ```name``` - оно станет его алиасом имени

4) **Изменение матрицы смежности**
```php
$manager->updateMatrix(int $y, int $x, float|int $value, bool $double = true)
```
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Например, можем сделать некий участок непроходимым - ```$manager->updateMatrix(1, 0, 1000000);```

5) **Запуск вычислительного процесса**
```php
$distance = $manager->run(int $iterations = 400)
```
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- для небольших графов, число итераций можно уменьшить.  
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Вернет найденное расстояние или ```null``` если поиск не увенчался успехом

6) **Получение найденного пути**
```php
$path = $manager->getInnerPath()
```
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Найденный путь из номеров нод.   
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Все ноды внутри именуются числами от 0 до N-1, где N - кол-во нод.  

7) **Получение найденного пути из алиасов**
```php
$path = $manager->getNamedPath()
```
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Найденный путь из алиасов имен нод, если вы их задавили.   

### Примеры

#### Решаем Задачу Коммивояжера классическим ACO
```php
use mgrechanik\aco\Manager;

$manager = new Manager();
$matrix = [
            [ 0, 8, 4, 11],
            [ 8, 0, 9, 5 ],
            [ 4, 9, 0, 8 ],
            [11, 5, 8, 0 ]
          ];
$manager->setMatrix($matrix);
$distance = $manager->run(20);
var_dump('Distance=' . $distance);
var_dump($manager->getInnerPath())
```
Получим:
```php
Distance=25

Array
(
    [0] => 0
    [1] => 1
    [2] => 3
    [3] => 2
    [4] => 0
) 
```

#### Решаем задачу "О кратчайшем пути", классическим ACO

```php
use mgrechanik\aco\Manager;
use mgrechanik\aco\SppTask;

$task = new SppTask(0, 3);
$manager = new Manager(task : $task);
$matrix = [
            [ 0 , 8, 4, 100],
            [ 8 , 0, 9, 5  ],
            [ 4 , 9, 0, 8  ],
            [100, 5, 8, 0  ]
          ];
$manager->setMatrix($matrix);   
$finder = $manager->getFinder();
// increase amount of ants to 6
$finder->setM(6);
$distance = $manager->run(50);
var_dump('Distance=' . $distance);
var_dump($manager->getInnerPath())
```
Получим:
```php
Distance=12

Array
(
    [0] => 0
    [1] => 2
    [2] => 3
)
// for comparison, the direct path [0, 3] is closed by big distance and distance of path [0, 1, 3] is 13
```

#### Загрузка данных в виде списка городов
```php
use mgrechanik\aco\Manager;
use mgrechanik\aco\City;

$cities = [new City(10,10), new City(50,50), new City(10,50), new City(60,10)];
$manager = new Manager();
$manager->setCities(...$cities);
```


#### Загрузка данных в виде матрицы смежности
```php
use mgrechanik\aco\Manager;

$matrix = [
            [ 0, 8, 4, 11],
            [ 8, 0, 9, 5 ],
            [ 4, 9, 0, 8 ] ,
            [11, 5, 8, 0 ]
          ];
$manager = new Manager();
$manager->setMatrix($matrix);
```

#### Используем поисковик c элитными муравьями

```php
$finder = new \mgrechanik\aco\elitist\Finder();
$manager = new Manager(finder : $finder);
//...
```

#### Смотрим историю работы, состоит из лучших путей, которые мы находили
```php
use mgrechanik\aco\Manager;

$matrix = [
            [ 0, 8, 4, 11],
            [ 8, 0, 9, 5 ],
            [ 4, 9, 0, 8 ] ,
            [11, 5, 8, 0 ]
          ];
$manager = new Manager();
$finder = $manager->getFinder();
$manager->setMatrix($matrix);
$manager->run();
var_dump($finder->getHistory());
```

#### Грузим список городов из картинки

При использовании [данной библиотеки](https://github.com/mgrechanik/image-points-searcher "Библиотека поиска точек на изображении") мы можем загрузить список городов из картинки, и результат поиска отобразить на результирующей картинке. Визуально будут картинки как на [Demo](#demo "картинки на демо, получены этим способом").   

Подробнее по подготовке картинки изучайте в описании к той библиотеке, но вкратце - на белом холсте отметьте кружочками диаметром 10 px, вершины графа, и используйте в коде ниже эту картинку.

```php
use mgrechanik\aco\Manager;
use mgrechanik\aco\City;

try {
    $imageSearcher = new \mgrechanik\imagepointssearcher\Searcher(
        './images/your_image.jpg',
    );
    $found = $imageSearcher->run();    
    if ($found > 1) {
        $points = $imageSearcher->getPoints();
        $cities = [];
        foreach ($points as $point) {
            $cities[] = new City($point['x'], $point['y']);
        }    
        $manager = new Manager();
        $manager->setCities(...$cities);
        if ($res = $manager->run()) {
            $innerPath = $manager->getInnerPath();
            $imageResult = new \mgrechanik\imagepointssearcher\ImageResult($imageSearcher);
            $imageResult->drawLabels();
            $imageResult->drawMargins();
            $imageResult->drawPath($innerPath);
            $imageResult->save('./images/result.jpg');
        }
    }
  
} catch (Exception $e) {
    //
}
```

---

## Настройка  <span id="settings"></span>   

### Настройка Поисковика

Основной настраиваемый обьект - Это поисковик.  
Получаем его:
```php
$manager = new Manager();
$finder = $manager->getFinder();
// Настраиваем
//$finder->set...
// ...
//$manager->run();
```

**Доступные настройки:**

- Установим величину расстояния, при которой участок между двумя узлами считается непроходимым  
```->setClosedPathValue(int $value)```

- Установим число муравьев  
```->setM(int $m)```

- Установим число муравьев как процент от числа нод. Используется по умолчанию (=40%)  
```->setMPercent(int $mPercent)```

- Устанавливаем коэффициенты для формулы
```php
->setAlpha(float $alpha);
->setBeta(float $beta);
->setP(float $p);
->setC(float $c);
->setQ(int $q);
```

- Установить стратегию выполнения математических задач  
```->setMathematics(MathematicsInterface $mathematics)```

- Установить текущую выполняемую задачу. TSP, SPP или др.  
```->setTask(Task $task)```

- Установим число элитных муравьев (для поисковика с элитными муравьями)   
```->setSigma(int $sigma)```

- Установим число элитных муравьев как процент от числа регулярных муравьев. Используется по умолчанию (=50%)   (для поисковика с элитными муравьями)  
```->setSigmaPercent(int $sPercent)```

---

## Производительность  <span id="performance"></span>   

> Прежде всего отключите XDebug или аналоги, т.к. они могут на порядок влиять на скорость работы

Данный ACO алгоритм находит [хорошие](#demo) пути на графе. И даже лучшие. 

Для примера возьмем задачу ```berlin52.tsp``` из библиотеки [TSPLIB95](#tsplib95), где 52 узла у графа.  
Решаем задачу кодом ниже:
```php
$cities = TspLibLoader::loadCitiesFromEuc2dFile(__DIR__ . '/images/data/berlin52.tsp');
$finder = new \mgrechanik\aco\elitist\Finder();
$finder->setSigmaPercent(150);
$finder->setMPercent(30);
$finder->setAlpha(0.7);
$manager = new Manager(finder : $finder);
$manager->setCities(...$cities);
$distance = $manager->run(300);
var_dump('Distance=' . $distance);
var_dump($finder->getHistory());
```
Получили вывод:
```php
Distance=7542

   Array ... 
    [85] => Array
        (
            [distance] => 7542
            [inner_path] => 0_21_30_17_2_16_20_41_6_1_29_22_19_49_28_15_45_43_33_34_35_38_39_36_37_47_23_4_14_5_3_24_11_27_26_25_46_12_13_51_10_50_32_42_9_8_7_40_18_44_31_48_0
            [iteration] => 85
            [time_spent] => 1.94 sec
        )  
    )
```

Данный код, работая на офисном компьютере, за менее чем 2 секунды, смог найти самый лучший путь из известных.

Здесь мы использовали поисковик на основе алгоритма с использования элитных муравьев. Он на практике дает лучшие результаты, чем классический.

Алгоритм вероятностный, каждый раз муравьи будут путешествовать по другому. И очень многое зависит от кол-ва нод, муравьев, настройки всех коэффициентов и параметров, участвующих в формулах.


---

## TSPLIB95 <span id="tsplib95"></span>

Библиотека [TSPLIB95](http://comopt.ifi.uni-heidelberg.de/software/TSPLIB95/), содержит множество ```Задач коммивояжера``` - исходные данные и решения - лучшие рузультаты, которые были когда либо найдены для этих задач ([пути](#tsplib95 "Пути располагаются в соответствующих файлах имя.opt.tour") и [расстояния](http://comopt.ifi.uni-heidelberg.de/software/TSPLIB95/STSP.html "Тут можно смотреть лучшие найденные расстояния")).

Библиотека ценна тем, что на ее данных можно протестировать эффективность наших алгоритмов, коэффициентов и параметров.

Библиотека предоставляет множество разных форматов исходных данных. Мы из коробки поддерживаем два из них.

### Подключение данных в виде списка X и Y координат. Расстояние Эвклидовое.

Пример файла с этим форматом - **berlin52.tsp** .
Загружаем из него список нод (городов) и передаем менеджеру:

```php
use mgrechanik\aco\TspLibLoader;
use mgrechanik\aco\Manager;

$fileName = __DIR__ . '/berlin52.tsp';
$cities = TspLibLoader::loadCitiesFromEuc2dFile($fileName);
$manager = new Manager();
$manager->setCities(...$cities);
```

### Подключение данных в виде матрицы смежности

Пример файла с этим форматом - **bays29.tsp** .

Загружаем из него матрицу смежности и передаем менеджеру:

```php
use mgrechanik\aco\TspLibLoader;
use mgrechanik\aco\Manager;

$fileName = __DIR__ . '/bays29.tsp';
$matrix = TspLibLoader::loadMatrixFromExplicitMatrixFile($fileName);
$manager = new Manager();
// tsplib95 library names nodes starting with "1"
$manager->setMatrix($matrix, 1);
```

---

## Терминология  <span id="terminology"></span>   

#### ```ACO``` - Муравьиный алгоритм (Ant colony optimization algorithm)

#### ```Nodes``` - Ноды, или узлы, или вершины графа. Или города. Муравьи путешествуют между ними

#### ```Adjacency Matrix``` - Матрица смежности, задает расстояния между узлами графа. Это базовая структура, с которой начинает работать алгоритм.
Если граф представлен как ```Города``` с координатами, то эта информация сначала переводится в матрицу смежности

#### ```Classic Finder``` - Поисковик, реализующий Классическую разновидность ACO алгоритма

#### ```Elitist Finder``` - Поисковик, реализующий алгоритм ACO с использованием элитных муравьев

#### ```Ant``` - муравей, рабочий юнит, который двигается по графу и ищет пути

#### ```Task``` - Задача, решаемая на графе. Например это может быть ```"Задача коммивояжера"```. Или задача ```"О кратчайшем пути"```. Или другая.

#### ```Manager``` - Менеджер, задача которого, сформировать Матрицу смежности, и передать ```Поисковику``` выполнение выбранной ```Задачи```.

#### ```Iteration``` - Итерация, во время которой, все муравьи находят по одному пути и оставляют феромоны. Количество итераций задаем сами

#### ```Pheromon``` - Феромон, то вещество, которое муравьи оставляют на найденных путях

#### ```m``` - Количество муравьев

#### ```mPercent``` - Количество муравьев в процентах относительно кол-ва узлов графа

#### ```sigma``` - Количество элитных муравьев, если используем соответствующий алгоритм

#### ```sigmaPercent``` - Количество элитных муравьев в процентах относительно кол-ва регулярных муравьев

#### ```alpha``` - коэффициент влияния обьема феромонов на выбор пути

#### ```beta``` - коэффициент влияния привлекательности пути

#### ```p``` - коэффициент испарения феромонов после каждой итерации

#### ```c``` - стартовое кол-во феромонов на всех путях

#### ```Q``` - Константа, участвующая в вычислении, сколько феромонов муравей оставляет на найденном пути
