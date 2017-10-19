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

* **$name**: 保存する設定値の名前。
* **$value**: 保存する値。PHPのどのデータ型でも扱えます。

#### 設定値を呼び出し

```php
$app['plugin.lib.service.state']->get($name, $default);
```

##### パラメーター

* **$name**: 呼び出す設定値の名前。
* **$default**: 設定値の登録がなかった場合に返す値。

#### ServiceProvider内で使用する場合

Stateサービスがアプリにまだ登録されていない場合がありますので、その場合はインスタンスを生成します。

```php
$StateService = new \Plugin\Lib\Service\StateService($app);
$StateService->get($name, $default);
```

### Cronイベント

### Cronの自動実行

### Cron実行キュー

## 開発者

[PineRay](https://github.com/pineray)

## ライセンス

[MIT](https://github.com/pineray/Library-Plugin-for-ECCUBE/blob/master/LICENSE)