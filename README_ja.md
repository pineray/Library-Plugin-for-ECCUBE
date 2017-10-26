ECCUBE 3.0.x用 汎用ツールプラグイン
====

このプラグインは、EC-CUBEのプラグイン開発において、あると便利な機能をまとめて提供するプラグインです。

## 機能

* Stateサービス: キーバリュー型の設定値を保存したり、読み込んだりできます。
* Cronイベント: プラグインが提供するパスにアクセスするとCronイベントが発生し、フックしている処理が実行されます。
* Cronの自動実行: 設定した時間ごとにCronイベントを自動で発生させます。
* Cron実行キュー: 細かく分割できる処理（たとえばCSVからのデータ登録など）をキューとして登録することで、Cronイベントの際にそれらが順に処理されます。
* ナビゲーションメニュー登録: 管理画面のナビゲーションメニューを登録できます。

## 使用方法

### Stateサービス

#### 設定値を保存

```php
$app['plugin.lib.service.state']->set($name, $value);
```

##### パラメーター

* **$name**: 保存する設定値の名前。必須。
* **$value**: 保存する値。PHPのどのデータ型でも扱えます。必須。

#### 設定値を呼び出し

```php
$app['plugin.lib.service.state']->get($name, $default);
```

##### パラメーター

* **$name**: 呼び出す設定値の名前。必須。
* **$default**: 設定値の登録がなかった場合に返す値。

#### ServiceProvider内で使用する場合

Stateサービスがアプリにまだ登録されていない場合がありますので、その場合はインスタンスを生成します。

```php
$StateService = new \Plugin\Lib\Service\StateService($app);
$StateService->get($name, $default);
```

### Cronイベント

* **hook名**: plugin.lib.cron.run
* **引数**: なし

/lib/cron/{cron_key} にアクセス、もしくは下記の自動実行で発生するイベントです。
{cron_key} はプラグインを有効化する際に生成され、プラグインの設定画面で確認できます。

### Cronの自動実行

上記のCronイベントを一定の間隔で自動的に発生させる機能です。
発生させる間隔はプラグインの管理画面から設定できます。

### Cron実行キュー

Cronの実行キューを登録しておくと、Cronイベントが発生した際、実行キューの処理が順に実行されます。

#### キューで実行する処理を作成する

プラグインディレクトリ直下に QueueExample というクラスがあるとして説明します。
そのクラスに processItem というパブリック関数を追加します。

```php
public class processItem($data) {
  // Add something...
}
```

クラスのインスタンスがアプリでシェアされていなければ、プラグインの ServiceProvider で以下のようにシェアします。

```php
$app['QUEUE_EXAMPLE'] = $app->share(function () use ($app) {
    return new \Plugin\PLUGINNAME\QueueExample($app);
});
```

#### Cron実行キューを登録する

```php
$app['plugin.lib.repository.Queue']->createItem($name, $data, $time);
```

##### パラメーター

* **$name**: アプリでシェアしたインスタンスのキー。上記の例では QUEUE_EXAMPLE となります。必須。
* **$data**: processItem関数へ渡す引数。PHPのどのデータ型でも扱えます。
* **$time**: 処理に要するおおよその時間。

### ナビゲーションメニュー登録

ServiceProvider内で以下のように記述することで、管理画面のナビゲーションメニューを追加できます。

```php
// メニュー内容の配列
$addNavi = [
    'id' => 'sample_nav_item',
    'name' => 'SAMPLEMENU',
    'has_child' => true,
    'child' => [
        [
            'id' => 'sample_nav_child_1',
            'name' => 'SAMPLECHILD1',
            'url' => 'plugin_sample_nav_child_1',
        ],
        [
            'id' => 'sample_nav_child_2',
            'name' => 'SAMPLECHILD2',
            'url' => 'plugin_sample_nav_child_2',
        ],
        [
            'id' => 'sample_nav_child_3',
            'name' => 'SAMPLECHILD3',
            'url' => 'plugin_sample_nav_child_3',
        ],
    ],
];

// アプリケーションに登録
$Nav = new \Plugin\Lib\Util\Nav($app);
$Nav->find('content');
$Nav->append($addNavi);
```

まずはナビゲーションユーティリティークラスのインスタンスを作成します。

```php
$Nav = new \Plugin\Lib\Util\Nav($app, $target);
```

##### パラメーター

* **$app**: 必須。
* **$target**: メニューを追加する起点となるメニュー項目のID。ここで指定した場合は次の find()　は実行する必要がありません。任意。 

次に、メニューを追加する起点となるメニュー項目を指定します。

```php
$Nav->find($target);
```

##### パラメーター

* **$target**: メニューを追加する起点となるメニュー項目のID。必須。

そして最後にメニュー項目を登録します。

```php
// 対象の子メニューの最後に追加
$Nav->append($item);
// 対象の子メニューの先頭に追加
$Nav->prepend($item);
// 対象のひとつ前に追加
$Nav->before($item);
// 対象のひとつ後に追加
$Nav->after($item);
```

##### パラメーター

* **$item**: 登録するメニュー項目の配列。必須。

find() と登録用の各種関数はチェーンメソッドで記述することもできます。

```php
$Nav->find($target)->append($item);
```

スタティック関数を使用することで、インスタンスを変数に格納することなくメニュー項目を登録することもできます。

```php
\Plugin\Lib\Util\Nav::forge($app)->find($target)->append($item);
```

## 開発者

[PineRay](https://github.com/pineray)

## ライセンス

[MIT](https://github.com/pineray/Library-Plugin-for-ECCUBE/blob/master/LICENSE)