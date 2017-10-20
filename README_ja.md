ECCUBE 3.0.x用 汎用ツールプラグイン
====

このプラグインは、EC-CUBEのプラグイン開発において、あると便利な機能をまとめて提供するプラグインです。

## 機能

* Stateサービス: キーバリュー型の設定値を保存したり、読み込んだりできます。
* Cronイベント: プラグインが提供するパスにアクセスするとCronイベントが発生し、フックしている処理が実行されます。
* Cronの自動実行: 設定した時間ごとにCronイベントを自動で発生させます。
* Cron実行キュー: 細かく分割できる処理（たとえばCSVからのデータ登録など）をキューとして登録することで、Cronイベントの際にそれらが順に処理されます。

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

## 開発者

[PineRay](https://github.com/pineray)

## ライセンス

[MIT](https://github.com/pineray/Library-Plugin-for-ECCUBE/blob/master/LICENSE)