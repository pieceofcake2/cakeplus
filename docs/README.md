# Cakeplus Documentation

Cakeplus is a CakePHP 2 plugin with a component, behaviors and a helper for common tasks,
especially in Japanese applications: HTML escaping of view data, multibyte and Japanese
validation rules, translated validation messages, reusable validation patterns and hidden
form fields.

- [Installation](#installation)
- [HtmlEscape Component](#htmlescape-component)
- [AddValidationRule Behavior](#addvalidationrule-behavior)
- [ValidationErrorI18n Behavior](#validationerrori18n-behavior)
- [ValidationPatterns Behavior](#validationpatterns-behavior)
- [Formhidden Helper](#formhidden-helper)

## Installation

Install the plugin with [Composer](https://getcomposer.org/):

```bash
composer require pieceofcake2/cakeplus
```

The plugin has the `cakephp-plugin` type, so it is installed into your plugins directory
by `composer/installers`. Set `installer-paths` in your application's `composer.json` so it
is installed into the right place:

| Layout | `installer-paths` |
| --- | --- |
| Modern (`config/`, `src/`, `templates/`, `plugins/`) | `"plugins/{$name}/": ["type:cakephp-plugin"]` |
| Legacy (`app/Config/`, `app/Plugin/`) | `"app/Plugin/{$name}/": ["type:cakephp-plugin"]` |

Load the plugin in `config/bootstrap.php` (legacy layout: `app/Config/bootstrap.php`).
If you already use `CakePlugin::loadAll()`, this is not necessary:

```php
CakePlugin::load('Cakeplus');
```

## HtmlEscape Component

`HtmlEscapeComponent::nl2br_h()` applies `h()` and `nl2br()` to every string in an array,
recursively. You can pass a list of keys (in `Model.field` form) that must not be escaped.

```php
class PostsController extends AppController
{
    public $components = ['Cakeplus.HtmlEscape'];

    public function index()
    {
        // Escape the paginated data, except Post.title
        $this->set('posts', $this->HtmlEscape->nl2br_h($this->paginate('Post'), null, ['Post.title']));
    }
}
```

The arguments are the value (array or string), the character set (`null` uses the
application encoding) and the list of keys to leave unescaped.

## AddValidationRule Behavior

Adds validation rules for multibyte strings and Japanese input. Attach the behavior to a
model or to your `AppModel`:

```php
public $actsAs = ['Cakeplus.AddValidationRule'];
```

The behavior sets the internal encoding (`mb_internal_encoding()`) to UTF-8. To use another
encoding, pass the `encoding` option:

```php
public $actsAs = ['Cakeplus.AddValidationRule' => ['encoding' => 'EUC-JP']];
```

Rules:

| Rule | Description |
| --- | --- |
| `maxLengthJP`, `$length` | At most `$length` characters (multibyte aware) |
| `minLengthJP`, `$length` | At least `$length` characters (multibyte aware) |
| `betweenJP`, `$low`, `$high` | Between `$low` and `$high` characters (multibyte aware) |
| `compare2fields`, `$field`, `$auth = false` | Same value as `$field` (e.g. `email` and `email_conf`) |
| `hiraganaOnly` | Only full-width hiragana (and `ー`) |
| `katakanaOnly` | Only full-width katakana (and `ー`, `゛`, `゜`) |
| `zenkakuOnly` | Only full-width (zenkaku) characters |
| `spaceOnly` | Fails when the value has only half-width or full-width spaces |
| `alphaNumber` | Only `0-9`, `a-z` and `A-Z` |
| `telFaxJp` | Japanese telephone or fax number (or an international `+` number) |
| `mobileEmailJp` | Email address whose local part has 3 to 30 characters of `a-z`, `0-9`, `.`, `_` and `-` (allows addresses of Japanese mobile carriers) |
| `passwordValid`, `$field`, `$min = 5`, `$max = 15` | `$field` has only alphanumeric characters and `$min` to `$max` characters |
| `datetime`, `$format = 'ymd'`, `$regex = null` | Valid date and time, e.g. `2026-10-14 12:34` |

The old snake_case names (`hiragana_only`, `katakana_only`, `zenkaku_only`, `space_only`,
`alpha_number`, `tel_fax_jp`, `mobile_email_jp`, `password_valid`) still work for backward
compatibility.

Example:

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

When the password field is hashed by the Auth component, set the third parameter of
`compare2fields` to `true`: the confirmation field is then hashed with `Security::hash()`
before the comparison.

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

## ValidationErrorI18n Behavior

Translates validation error messages with `__()`, and replaces the messages of some rules
with common messages. Attach the behavior to your `AppModel` and call
`replaceValidationErrorMessagesI18n()` in `beforeValidate()`:

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

Messages are looked up by the name of the validation rule (the key in `$validate`). The
default messages are:

| Rule name | Message |
| --- | --- |
| `require` | Please be sure to input. |
| `email_invalid` | Invalid Email address. |
| `between` | Between %2$d and %3$d characters. |

Messages are formatted with `vsprintf()` and the rule's parameters, so `%2$d` is the first
parameter of the rule. Other messages are passed through `__()`.

To prefix each message with the translated field name, set the `withFieldName` option:

```php
public $actsAs = ['Cakeplus.ValidationErrorI18n' => ['withFieldName' => true]];
```

To add or override messages in a model, call `setErrorMessageI18n()`:

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

The second parameter of `setErrorMessageI18n()` controls how the messages are combined
with the defaults: `false` merges them, `true` replaces all default messages.

## ValidationPatterns Behavior

Defines named sets of validation rules that can be reused in `$validate`. Attach the
behavior to your `AppModel` and call `setValidationPatterns()` in `beforeValidate()`:

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

Patterns defined in `AppModel` can be used by all models. Use the pattern name in
`$validate` instead of the rules:

```php
public $validate = [
    'email' => 'email_pattern',
];
```

You can also combine patterns and rules in an array:

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

## Formhidden Helper

`FormhiddenHelper::hiddenVars()` creates hidden fields for all values of the request data,
or of the array you pass. It is useful for confirmation screens that post the form data
again.

```php
public $helpers = ['Cakeplus.Formhidden'];
```

In the view:

```php
// Hidden fields for $this->request->data
echo $this->Formhidden->hiddenVars();

// Hidden fields for an array, e.g. ['Post' => ['title' => 'Hello']]
echo $this->Formhidden->hiddenVars($data);
```

## Origin

This package is a maintained fork of [ichikaway/cakeplus](https://github.com/ichikaway/cakeplus)
(`ichikaway/cakeplus` on Packagist, last upstream version v2.0), originally by Yasushi Ichikawa. It
is maintained for CakePHP 2 on modern PHP by [pieceofcake2](https://github.com/pieceofcake2).
Changes from the original are listed in the
[commit history since the fork](https://github.com/pieceofcake2/cakeplus/compare/a273a8a...2.x).
