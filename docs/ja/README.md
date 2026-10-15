# Cakeplus ドキュメント

Cakeplus は、特に日本語のアプリケーションでよく必要になる処理のためのコンポーネント、ビヘイビア、
ヘルパーを提供する CakePHP 2 プラグインです。ビューに渡すデータの HTML エスケープ、マルチバイト文字や
日本語のためのバリデーションルール、バリデーションメッセージの翻訳、再利用できるバリデーションパターン、
hidden フィールドを扱えます。

- [インストール](#installation)
- [HtmlEscape コンポーネント](#htmlescape-component)
- [AddValidationRule ビヘイビア](#addvalidationrule-behavior)
- [ValidationErrorI18n ビヘイビア](#validationerrori18n-behavior)
- [ValidationPatterns ビヘイビア](#validationpatterns-behavior)
- [Formhidden ヘルパー](#formhidden-helper)

<a id="installation"></a>

## インストール

[Composer](https://getcomposer.org/) でプラグインをインストールします:

```bash
composer require pieceofcake2/cakeplus
```

このプラグインは `cakephp-plugin` タイプなので、`composer/installers` によってプラグインディレクトリに
インストールされます。正しい場所にインストールされるように、アプリケーションの `composer.json` で
`installer-paths` を設定してください:

| レイアウト | `installer-paths` |
| --- | --- |
| モダン (`config/`, `src/`, `templates/`, `plugins/`) | `"plugins/{$name}/": ["type:cakephp-plugin"]` |
| レガシー (`app/Config/`, `app/Plugin/`) | `"app/Plugin/{$name}/": ["type:cakephp-plugin"]` |

`config/bootstrap.php` (レガシーレイアウトでは `app/Config/bootstrap.php`) でプラグインをロードします。
すでに `CakePlugin::loadAll()` を使っている場合は不要です:

```php
CakePlugin::load('Cakeplus');
```

<a id="htmlescape-component"></a>

## HtmlEscape コンポーネント

`HtmlEscapeComponent::nl2br_h()` は、配列内のすべての文字列に再帰的に `h()` と `nl2br()` を適用します。
エスケープしてはいけないキーのリストを (`Model.field` 形式で) 渡すこともできます。

```php
class PostsController extends AppController
{
    public $components = ['Cakeplus.HtmlEscape'];

    public function index()
    {
        // Post.title 以外のページネーションされたデータをエスケープします
        $this->set('posts', $this->HtmlEscape->nl2br_h($this->paginate('Post'), null, ['Post.title']));
    }
}
```

引数は、値 (配列または文字列)、文字セット (`null` の場合はアプリケーションのエンコーディング)、
エスケープせずに残すキーのリストです。

<a id="addvalidationrule-behavior"></a>

## AddValidationRule ビヘイビア

マルチバイト文字列と日本語の入力のためのバリデーションルールを追加します。ビヘイビアをモデルか
`AppModel` に追加します:

```php
public $actsAs = ['Cakeplus.AddValidationRule'];
```

このビヘイビアは内部エンコーディング (`mb_internal_encoding()`) を UTF-8 に設定します。
別のエンコーディングを使うには、`encoding` オプションを渡してください:

```php
public $actsAs = ['Cakeplus.AddValidationRule' => ['encoding' => 'EUC-JP']];
```

ルール:

| ルール | 説明 |
| --- | --- |
| `maxLengthJP`, `$length` | `$length` 文字以下 (マルチバイト対応) |
| `minLengthJP`, `$length` | `$length` 文字以上 (マルチバイト対応) |
| `betweenJP`, `$low`, `$high` | `$low` 文字以上 `$high` 文字以下 (マルチバイト対応) |
| `compare2fields`, `$field`, `$auth = false` | `$field` と同じ値 (例えば `email` と `email_conf`) |
| `hiraganaOnly` | 全角ひらがな (と `ー`) のみ |
| `katakanaOnly` | 全角カタカナ (と `ー`、`゛`、`゜`) のみ |
| `zenkakuOnly` | 全角文字のみ |
| `spaceOnly` | 値が半角または全角のスペースだけの場合にエラー |
| `alphaNumber` | `0-9`、`a-z`、`A-Z` のみ |
| `telFaxJp` | 日本の電話番号または FAX 番号 (または `+` で始まる国際番号) |
| `mobileEmailJp` | ローカル部が `a-z`、`0-9`、`.`、`_`、`-` の 3 ～ 30 文字のメールアドレス (日本の携帯キャリアのアドレスを許可します) |
| `passwordValid`, `$field`, `$min = 5`, `$max = 15` | `$field` が英数字のみで `$min` ～ `$max` 文字 |
| `datetime`, `$format = 'ymd'`, `$regex = null` | 有効な日時 (例えば `2026-10-14 12:34`) |

以前のスネークケースの名前 (`hiragana_only`、`katakana_only`、`zenkaku_only`、`space_only`、
`alpha_number`、`tel_fax_jp`、`mobile_email_jp`、`password_valid`) も、後方互換性のために引き続き
使えます。

例:

```php
public $validate = [
    'name' => [
        'maxLength' => [
            'rule' => ['maxLengthJP', 5],
            'message' => 'Up to 5 characters.',
        ],
        'minLength' => [
            'rule' => ['minLengthJP', 2],
            'message' => 'At least 2 characters.',
        ],
        'between' => [
            'rule' => ['betweenJP', 2, 5],
            'message' => 'Between 2 and 5 characters.',
        ],
        'notSpaceOnly' => [
            'rule' => ['spaceOnly'],
            'message' => 'Enter something other than spaces.',
        ],
    ],
    'name_kana' => [
        'katakana' => [
            'rule' => ['katakanaOnly'],
            'message' => 'Use katakana only.',
        ],
    ],
    'email' => [
        'confirm' => [
            'rule' => ['compare2fields', 'email_conf'],
            'message' => 'The email addresses do not match.',
        ],
    ],
    'published' => [
        'datetime' => [
            'rule' => ['datetime'],
            'message' => 'Enter a valid date and time.',
        ],
    ],
];
```

パスワードのフィールドが Auth コンポーネントでハッシュ化される場合は、`compare2fields` の 3 番目の
パラメータを `true` にしてください。確認用のフィールドが比較の前に `Security::hash()` でハッシュ化
されます。

```php
public $validate = [
    'password' => [
        'confirm' => [
            'rule' => ['compare2fields', 'password_conf', true],
            'message' => 'The passwords do not match.',
        ],
    ],
];
```

<a id="validationerrori18n-behavior"></a>

## ValidationErrorI18n ビヘイビア

バリデーションエラーのメッセージを `__()` で翻訳し、一部のルールのメッセージを共通のメッセージに
置き換えます。ビヘイビアを `AppModel` に追加し、`beforeValidate()` で
`replaceValidationErrorMessagesI18n()` を呼び出します:

```php
class AppModel extends Model
{
    public $actsAs = ['Cakeplus.ValidationErrorI18n'];

    public function beforeValidate($options = []): bool
    {
        $this->replaceValidationErrorMessagesI18n();

        return true;
    }
}
```

メッセージは、バリデーションルールの名前 (`$validate` のキー) で検索されます。デフォルトの
メッセージは次のとおりです:

| ルール名 | メッセージ |
| --- | --- |
| `require` | Please be sure to input. |
| `email_invalid` | Invalid Email address. |
| `between` | Between %2$d and %3$d characters. |

メッセージはルールのパラメータを使って `vsprintf()` で整形されるので、`%2$d` はルールの最初の
パラメータになります。その他のメッセージは `__()` に渡されます。

各メッセージの前に翻訳されたフィールド名を付けるには、`withFieldName` オプションを設定します:

```php
public $actsAs = ['Cakeplus.ValidationErrorI18n' => ['withFieldName' => true]];
```

モデルでメッセージを追加したり上書きしたりするには、`setErrorMessageI18n()` を呼び出します:

```php
class Post extends AppModel
{
    public function beforeValidate($options = []): bool
    {
        $this->setErrorMessageI18n([
            'invalid_email' => __('Invalid Email !!!.'),
        ], false);

        return parent::beforeValidate($options);
    }
}
```

`setErrorMessageI18n()` の 2 番目のパラメータは、メッセージをデフォルトとどう組み合わせるかを
指定します。`false` ならマージし、`true` ならデフォルトのメッセージをすべて置き換えます。

<a id="validationpatterns-behavior"></a>

## ValidationPatterns ビヘイビア

`$validate` で再利用できる、名前付きのバリデーションルールのセットを定義します。ビヘイビアを
`AppModel` に追加し、`beforeValidate()` で `setValidationPatterns()` を呼び出します:

```php
class AppModel extends Model
{
    public $actsAs = ['Cakeplus.ValidationPatterns'];

    public $validation_patterns = [
        'email_pattern' => [
            'notBlankMail' => [
                'rule' => 'notBlank',
                'last' => true,
            ],
            'validMail' => [
                'rule' => 'email',
                'allowEmpty' => true,
                'last' => true,
            ],
            'uniqueMail' => [
                'rule' => 'isUnique',
                'on' => 'create',
            ],
        ],
    ];

    public function beforeValidate($options = []): bool
    {
        $this->setValidationPatterns();

        return true;
    }
}
```

`AppModel` で定義したパターンは、すべてのモデルで使えます。`$validate` では、ルールの代わりに
パターン名を使います:

```php
public $validate = [
    'email' => 'email_pattern',
];
```

配列の中でパターンとルールを組み合わせることもできます:

```php
public $validate = [
    'email' => [
        'email_pattern',
        'maxLength' => [
            'rule' => ['maxLength', 100],
        ],
    ],
];
```

<a id="formhidden-helper"></a>

## Formhidden ヘルパー

`FormhiddenHelper::hiddenVars()` は、リクエストデータ、または渡した配列のすべての値について hidden
フィールドを作成します。フォームのデータを再度送信する確認画面で便利です。

```php
public $helpers = ['Cakeplus.Formhidden'];
```

ビューでは次のようにします:

```php
// $this->request->data の hidden フィールド
echo $this->Formhidden->hiddenVars();

// 配列の hidden フィールド。例えば ['Post' => ['title' => 'Hello']]
echo $this->Formhidden->hiddenVars($data);
```

<a id="origin"></a>

## 由来

このパッケージは、Yasushi Ichikawa 氏が作成した [ichikaway/cakeplus](https://github.com/ichikaway/cakeplus)
(Packagist では `ichikaway/cakeplus`、アップストリームの最終バージョンは v2.0) をフォークしてメンテナンス
しているものです。[pieceofcake2](https://github.com/pieceofcake2) が、モダンな PHP で動く CakePHP 2
向けにメンテナンスしています。オリジナルからの変更点は
[フォーク以降のコミット履歴](https://github.com/pieceofcake2/cakeplus/compare/a273a8a...2.x) を参照してください。
